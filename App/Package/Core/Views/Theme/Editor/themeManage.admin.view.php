<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Theme\Editor\EditorType;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

/* @var array $themeConfigs */
/* @var array $themeMenus */

$formattedConfigs = [];

foreach ($themeConfigs as $config) {
    $formattedConfigs[$config['theme_config_name']] = $config['theme_config_value'];
}

$editorSettings = [];

foreach ($themeMenus as $menu) {
    foreach ($menu->values as $value) {
        $key = $menu->key . '_' . $value->themeKey;

        $editorSettings[$key] = [
            'default' => $value->defaultValue,
            'type' => $value->type,
        ];

        if ($value->type === EditorType::RANGE && isset($value->rangeOptions[0])) {
            $range = $value->rangeOptions[0];
            $editorSettings[$key]['prefix'] = $range->getPrefix();
            $editorSettings[$key]['suffix'] = $range->getSuffix();
            $editorSettings[$key]['min'] = $range->getMin();
            $editorSettings[$key]['max'] = $range->getMax();
            $editorSettings[$key]['step'] = $range->getStep();
        }
    }
}

Website::setTitle(LangManager::translate('core.theme.manage.title', ['Theme' => ThemeManager::getInstance()->getCurrentTheme()->name()]));
Website::setDescription(LangManager::translate('core.theme.manage.description'));

?>
<div class="preview-container">
    <iframe id="previewFrame" width="98%" style="height: 100%;" src="<?= Website::getUrl() ?>/?editor=1"></iframe>
</div>

<!--  MENU ET NAVIGATION DYNAMIQUE  -->
<script>
    function showSection(index) {
        const button = document.querySelectorAll("#menuSections ul li button")[index];
        const title = button.getAttribute("data-title");
        const scope = button.getAttribute("data-scope");
        const menuKey = button.getAttribute("data-menukey");

        document.getElementById("menuSections").classList.add("hidden");
        document.getElementById("editorSection").classList.remove("hidden");
        document.getElementById("sectionTitle").innerText = title;

        const iframe = document.getElementById("previewFrame");
        const targetUrl = getEditorUrl(scope);
        if (!urlsAreEqual(iframe.src, targetUrl)) iframe.src = targetUrl;

        // cacher toutes les sections
        document.querySelectorAll(".theme-section").forEach(el => el.classList.add("hidden"));

        // afficher la bonne
        const section = document.getElementById(`section_${menuKey}`);
        section.classList.remove("hidden");

        // déplacer dans le container d'édition
        document.getElementById("sectionContent").innerHTML = "";
        document.getElementById("sectionContent").appendChild(section);
    }

    function backToMenu() {
        document.getElementById("editorSection").classList.add("hidden");
        document.getElementById("menuSections").classList.remove("hidden");

        const iframe = document.getElementById("previewFrame");
        const targetUrl = getEditorUrl();
        const section = document.querySelector("#sectionContent .theme-section");
        if (section) {
            section.classList.add("hidden");
            document.getElementById("allSections").appendChild(section);
        }
        if (!urlsAreEqual(iframe.src, targetUrl)) {
            iframe.src = targetUrl;
        }
    }
</script>

<!--  MISES à JOUR EN LIVE DE L'IFRAME  -->
<script>
    const configValues = <?= json_encode($formattedConfigs) ?>;
    const editorSettings = <?= json_encode($editorSettings) ?>;
    const imagePreviews = {};
    document.addEventListener("DOMContentLoaded", () => {
        const iframe = document.getElementById("previewFrame");

        iframe.onload = function () {
            updateThemePreview();
        };

        function updateThemePreview(keyToUpdate = null) {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            if (!iframeDoc) return;

            // 🔹 Texte brut : <span data-cmw="menu:key">
            iframeDoc.querySelectorAll('[data-cmw]').forEach(el => {
                const ref = el.getAttribute("data-cmw");
                if (!ref || !ref.includes(":")) return;

                const [menuKey, valueKey] = ref.split(":");
                const fullKey = `${menuKey}_${valueKey}`;
                if (keyToUpdate && fullKey !== keyToUpdate) return;

                el.textContent = configValues[fullKey] || "";
            });

            // 🔹 Attributs : <span data-cmw-attr="src:menu:key">
            iframeDoc.querySelectorAll('[data-cmw-attr]').forEach(el => {
                const attrDefs = el.getAttribute("data-cmw-attr").trim().split(/\s+/);

                attrDefs.forEach(def => {
                    const [attrName, menuKey, valueKey] = def.split(":");
                    const fullKey = `${menuKey}_${valueKey}`;
                    if (keyToUpdate && fullKey !== keyToUpdate) return;

                    let value = configValues[fullKey] || "";

                    // Récupère les infos du champ
                    const setting = editorSettings?.[fullKey];
                    const isImage = setting?.type === "image";
                    const defaultValue = setting?.default;

                    if (isImage) {
                        const themeName = "<?= ThemeManager::getInstance()->getCurrentTheme()->name() ?>";
                        value = getImageUrl(themeName, value, defaultValue);
                    }

                    el.setAttribute(attrName, value);
                });
            });



            // 🔹 Style CSS : <span data-cmw-style="color:menu:key">
            iframeDoc.querySelectorAll('[data-cmw-style]').forEach(el => {
                const styles = el.getAttribute("data-cmw-style").split(";");

                styles.forEach(entry => {
                    const parts = entry.split(":");
                    if (parts.length < 3) return;

                    const cssProp = parts[0].trim();
                    const menuKey = parts[1];
                    const valueKey = parts.slice(2).join(":");
                    const fullKey = `${menuKey}_${valueKey}`;

                    if (keyToUpdate && fullKey !== keyToUpdate) return;

                    let rawValue = configValues[fullKey] || "";
                    const setting = editorSettings?.[fullKey] || {};
                    const isSlider = setting.type === "range";
                    const suffix = setting.suffix || "";
                    const prefix = setting.prefix || "";

                    // Gestion image
                    const urlProps = ["background-image", "background", "list-style-image", "mask-image"];
                    const lengthProps = [
                        "width", "height", "top", "left", "right", "bottom", "margin", "padding", "font-size",
                        "gap", "max-width", "min-width", "max-height", "min-height", "border-radius"
                    ];

                    let value = rawValue;

                    if (urlProps.includes(cssProp) && !rawValue.includes("url(")) {
                        value = `url('${rawValue}')`;
                    } else if (isSlider || lengthProps.includes(cssProp)) {
                        // Si slider ou propriété CSS numérique
                        if (/^-?\d+(\.\d+)?$/.test(rawValue)) {
                            value = `${prefix}${rawValue}${suffix}`;
                        }
                    }

                    el.style.setProperty(cssProp, value);
                });
            });


            // 🔹 Classe dynamique : <div data-cmw-class="menu:key">
            iframeDoc.querySelectorAll('[data-cmw-class]').forEach(el => {
                const refs = el.getAttribute("data-cmw-class").trim().split(/\s+/);

                refs.forEach(ref => {
                    const [menuKey, valueKey] = ref.split(":");
                    const fullKey = `${menuKey}_${valueKey}`;
                    if (keyToUpdate && fullKey !== keyToUpdate) return;

                    const rawValue = configValues[fullKey] || "";
                    const setting = editorSettings?.[fullKey] || {};

                    let finalClass = rawValue;

                    if (setting.type === "range") {
                        const prefix = setting.prefix || "";
                        const suffix = setting.suffix || "";
                        finalClass = `${prefix}${rawValue}${suffix}`;
                    }

                    const attrKey = `data-cmw-last-class-${fullKey}`;

                    // Supprimer l'ancienne classe
                    const oldClass = el.getAttribute(attrKey);
                    if (oldClass) el.classList.remove(oldClass);

                    // Ajouter la nouvelle classe
                    if (finalClass) {
                        el.classList.add(finalClass);
                        el.setAttribute(attrKey, finalClass);
                    } else {
                        el.removeAttribute(attrKey);
                    }
                });
            });


            // 🔹 Visibilité conditionnelle : <div data-cmw-visible="menu:key">
            iframeDoc.querySelectorAll('[data-cmw-visible]').forEach(el => {
                const [menuKey, valueKey] = el.getAttribute("data-cmw-visible").split(":");
                const fullKey = `${menuKey}_${valueKey}`;
                if (keyToUpdate && fullKey !== keyToUpdate) return;

                const value = configValues[fullKey];
                el.style.display = (value === "0" || value === "" || value === null) ? "none" : "";
            });

            // 🔹 Inline JS / JSON : const foo = "__CMW:menu:key__";
            iframeDoc.querySelectorAll("script").forEach(script => {
                if (!script.textContent.includes("__CMW:")) return;

                const newContent = script.textContent.replace(/__CMW:([\w-]+):([\w-]+)__/g, (_, menuKey, valueKey) => {
                    const fullKey = `${menuKey}_${valueKey}`;
                    return configValues[fullKey] || "";
                });

                const newScript = iframeDoc.createElement("script");
                newScript.textContent = newContent;
                script.replaceWith(newScript);
            });

            // 🔁 Réinjecte les images temporaires non uploadées
            Object.entries(imagePreviews).forEach(([fullKey, previewUrl]) => {
                iframeDoc.querySelectorAll('[data-cmw-attr]').forEach(el => {
                    const attrDefs = el.getAttribute("data-cmw-attr").trim().split(/\s+/);
                    attrDefs.forEach(def => {
                        const [attrName, menuKey, key] = def.split(":");
                        const defKey = `${menuKey}_${key}`;
                        if (defKey === fullKey && attrName === "src") {
                            el.setAttribute("src", previewUrl);
                        }
                    });
                });
            });
        }

        // Écoute les modifications des inputs et met à jour uniquement l'élément concerné
        document.getElementById("sectionContent").addEventListener("input", (event) => {
            const valueKey = event.target.name;
            if (!valueKey) return;
            configValues[valueKey] = event.target.value.trim();
            updateThemePreview(valueKey);
        });

        // Gère aussi les cases à cocher / et les change
        document.getElementById("sectionContent").addEventListener("change", (event) => {
            const target = event.target;
            const valueKey = target.name;
            if (!valueKey) return;

            // ✅ Gestion des fichiers image (preview temporaire sans upload)
            if (target.type === "file" && target.files.length > 0) {
                const file = target.files[0];

                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewUrl = e.target.result;

                    imagePreviews[valueKey] = previewUrl; // ✅ sauvegarde locale

                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    iframeDoc.querySelectorAll('[data-cmw-attr]').forEach(el => {
                        const attrDefs = el.getAttribute("data-cmw-attr").trim().split(/\s+/);
                        attrDefs.forEach(def => {
                            const [attrName, menuKey, key] = def.split(":");
                            const fullKey = `${menuKey}_${key}`;
                            if (fullKey === valueKey && attrName === "src") {
                                el.setAttribute("src", previewUrl);
                            }
                        });
                    });
                };


                reader.readAsDataURL(file);
                return; // Ne pas modifier configValues tant que pas uploadé
            }

            // ✅ Gestion classique texte/checkbox
            if (target.type === "checkbox") {
                configValues[valueKey] = target.checked ? "1" : "0";
            } else {
                configValues[valueKey] = target.value.trim();
            }

            updateThemePreview(valueKey);
        });
    });
</script>

<!--  UTILITAIRES  -->
<script>
    function getEditorUrl(scope = null) {
        const baseUrl = "<?= Website::getUrl() ?>";
        const rawUrl = scope ? `${baseUrl}${scope}` : baseUrl;

        try {
            const url = new URL(rawUrl, window.location.origin);
            url.searchParams.set('editor', '1');
            return url.href;
        } catch (e) {
            return rawUrl.includes('?') ? `${rawUrl}&editor=1` : `${rawUrl}?editor=1`;
        }
    }

    function urlsAreEqual(url1, url2) {
        try {
            const u1 = new URL(url1, window.location.origin);
            const u2 = new URL(url2, window.location.origin);

            return (
                u1.origin === u2.origin &&
                u1.pathname.replace(/\/+$/, '') === u2.pathname.replace(/\/+$/, '') &&
                u1.search === u2.search
            );
        } catch {
            return url1 === url2;
        }
    }

    function getImageUrl(themeName, rawValue, defaultValue) {
        const base = "<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>";

        if (!rawValue || rawValue === defaultValue) {
            return `${base}Public/Themes/${themeName}/${defaultValue}`;
        }

        return `${base}Public/Uploads/${themeName}/Img/${rawValue}`;
    }

</script>

<!--  PROTEGE LES LIENS ET LES POSTS  -->
<script>
    document.getElementById("previewFrame").addEventListener("load", () => {
        const iframe = document.getElementById("previewFrame");
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;

        if (!iframeDoc) return;

        const showEditorWarning = () => {
            iziToast.show({
                titleSize: '14',
                messageSize: '12',
                icon: 'fa-solid fa-xmark',
                title: "Éditeur",
                message: "Vous ne pouvez pas faire ceci en mode éditeur.",
                color: "#faaa38",
                iconColor: '#ffffff',
                titleColor: '#ffffff',
                messageColor: '#fff',
                balloon: false,
                close: true,
                pauseOnHover: true,
                position: 'topCenter',
                timeout: 4000,
                animateInside: false,
                progressBar: true,
                transitionIn: 'fadeInDown',
                transitionOut: 'fadeOut',
            });
        };

        const protectElements = () => {
            iframeDoc.querySelectorAll('a:not([data-protected]), form:not([data-protected])').forEach(el => {
                el.setAttribute('data-protected', 'true');

                if (el.tagName.toLowerCase() === 'a') {
                    el.addEventListener('click', e => {
                        e.preventDefault();
                        showEditorWarning();
                    });
                }

                if (el.tagName.toLowerCase() === 'form') {
                    el.addEventListener('submit', e => {
                        e.preventDefault();
                        showEditorWarning();
                    });
                }
            });
        };

        // Appliquer une première fois
        protectElements();

        // Observer les changements
        const observer = new MutationObserver(() => {
            protectElements();
        });

        observer.observe(iframeDoc.body, { childList: true, subtree: true });
    });
</script>

<!--  SAUVEGARDE AJAX -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("ThemeSettings");
        const submitButton = document.getElementById("submitButton");

        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            // Désactiver le bouton et ajouter un loader
            submitButton.disabled = true;
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = `<i class="fa fa-spinner fa-spin"></i> <?= LangManager::translate('core.btn.save') ?>`;

            const formData = new FormData(form);
            for (let pair of formData.entries()) {
                console.log(pair[0], pair[1]);
            }

            form.querySelectorAll('input[type="checkbox"]').forEach(input => {
                if (!formData.has(input.name)) {
                    formData.append(input.name, "0");
                }
            });

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                });

                const result = await response.json();

                // Gérer la réponse réussie
                if (result.success) {
                    const csrfTokenField = document.querySelector('[name="security-csrf-token"]');
                    const csrfTokenIdField = document.querySelector('[name="security-csrf-token-id"]');

                    if (csrfTokenField && csrfTokenIdField) {
                        csrfTokenField.value = result.new_csrf_token;
                        csrfTokenIdField.value = result.new_csrf_token_id;
                    } else {
                        console.error("Champs CSRF introuvables");
                    }

                    iziToast.show({
                        titleSize: '14',
                        messageSize: '12',
                        icon: 'fa-solid fa-check',
                        title: "<?= LangManager::translate('core.toaster.success') ?>",
                        message: "<?= LangManager::translate('core.toaster.config.success') ?>",
                        color: "#20b23a",
                        iconColor: '#ffffff',
                        titleColor: '#ffffff',
                        messageColor: '#ffffff',
                        balloon: false,
                        close: true,
                        pauseOnHover: true,
                        position: 'topCenter',
                        timeout: 4000,
                        animateInside: false,
                        progressBar: true,
                        transitionIn: 'fadeInDown',
                        transitionOut: 'fadeOut',
                    });
                } else {
                    // Gérer la réponse échouée
                    iziToast.show({
                        titleSize: '14',
                        messageSize: '12',
                        icon: 'fa-solid fa-xmark',
                        title: "<?= LangManager::translate('core.toaster.error') ?>",
                        message: result.error || "<?= LangManager::translate('core.toaster.config.error') ?>",
                        color: "#ab1b1b",
                        iconColor: '#ffffff',
                        titleColor: '#ffffff',
                        messageColor: '#ffffff',
                        balloon: false,
                        close: true,
                        pauseOnHover: true,
                        position: 'topCenter',
                        timeout: 4000,
                        animateInside: false,
                        progressBar: true,
                        transitionIn: 'fadeInDown',
                        transitionOut: 'fadeOut',
                    });
                }
            } catch (error) {
                // Gérer les erreurs réseau ou exceptions
                iziToast.show({
                    titleSize: '14',
                    messageSize: '12',
                    icon: 'fa-solid fa-xmark',
                    title: "<?= LangManager::translate('core.toaster.error') ?>",
                    message: "<?= LangManager::translate('core.toaster.internalError') ?>, actualiser la page !",
                    color: "#ab1b1b",
                    iconColor: '#ffffff',
                    titleColor: '#ffffff',
                    messageColor: '#ffffff',
                    balloon: false,
                    close: true,
                    pauseOnHover: true,
                    position: 'topCenter',
                    timeout: 4000,
                    animateInside: false,
                    progressBar: true,
                    transitionIn: 'fadeInDown',
                    transitionOut: 'fadeOut',
                });
                console.error("Une erreur est survenue :", error);
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
    });
</script>