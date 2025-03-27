<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var array $themeConfigs */

$formattedConfigs = [];

foreach ($themeConfigs as $config) {
    $formattedConfigs[$config['theme_config_name']] = $config['theme_config_value'];
}
Website::setTitle(LangManager::translate('core.theme.manage.title', ['Theme' => ThemeManager::getInstance()->getCurrentTheme()->name()]));
Website::setDescription(LangManager::translate('core.theme.manage.description'));

//TODO Gérer les element visible ou non en js peut être un commentaire : <!-- CMW:IF:key1:key2--> jusqu'a <!-- CMW:ENDIF:key1:key2-->
//TODO Gérer les images (en se basant sur le type de value)
//TODO Ajouter des truc colle comme le fa picker etc ...
?>
<style>
    input[type='color'] {
        -webkit-appearance: none;
        border: transparent;
        width: 100%;
        height: 20px;
        cursor: pointer;
        padding: 0;
        border-radius: 6px;
    }

    input[type='color']::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    input[type='color']::-webkit-color-swatch {
        border: none;
    }
    input[type='color']::-moz-color-swatch {
        border: none;
    }
</style>

<div class="preview-container">
    <iframe id="previewFrame" width="98%" style="height: 100%;" src="<?= Website::getUrl() ?>/?editor=1"></iframe>
</div>

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

    function showSection(index) {
        const button = document.querySelectorAll("#menuSections ul li button")[index];
        const title = button.getAttribute("data-title");
        const values = JSON.parse(button.getAttribute("data-values"));
        const scope = button.getAttribute("data-scope");
        const menuKey = button.getAttribute("data-menukey");

        document.getElementById("menuSections").classList.add("hidden");
        document.getElementById("editorSection").classList.remove("hidden");

        document.getElementById("sectionTitle").innerText = title;

        const iframe = document.getElementById("previewFrame");
        const targetUrl = getEditorUrl(scope);

        if (!urlsAreEqual(iframe.src, targetUrl)) {
            iframe.src = targetUrl;
        }

        let contentHtml = "";
        values.forEach(value => {
            contentHtml += generateInputField(value, menuKey);
        });

        document.getElementById("sectionContent").innerHTML = contentHtml;
    }

    function backToMenu() {
        document.getElementById("editorSection").classList.add("hidden");
        document.getElementById("menuSections").classList.remove("hidden");

        const iframe = document.getElementById("previewFrame");
        const targetUrl = getEditorUrl();

        if (!urlsAreEqual(iframe.src, targetUrl)) {
            iframe.src = targetUrl;
        }
    }


    let configValues = <?= json_encode($formattedConfigs) ?>;
    function generateInputField(value, menuKey) {
        const fullKey = `${menuKey}_${value.themeKey}`;

        switch (value.type) {
            case 'color':
                return `
        <div class="mx-auto">
            <label for="${value.themeKey}">${value.title}</label>
            <input type="color" data-menu-key="${menuKey}" id="${value.themeKey}" name="${value.themeKey}" class="input"
                value="${configValues[fullKey] ?? value.defaultValue ?? ''}">
        </div>
        `;

            case 'number':
                return `
        <div>
            <label for="${value.themeKey}">${value.title}</label>
            <input type="number" data-menu-key="${menuKey}" id="${value.themeKey}" name="${value.themeKey}" class="input"
                value="${configValues[fullKey] ?? value.defaultValue ?? ''}">
        </div>
        `;

            case 'text':
                return `
        <div>
            <label for="${value.themeKey}">${value.title}</label>
            <input type="text" data-menu-key="${menuKey}" id="${value.themeKey}" name="${value.themeKey}" class="input" placeholder="Default"
                value="${configValues[fullKey] ?? value.defaultValue ?? ''}">
        </div>
        `;

            case 'textarea':
            case 'css':
                return `
        <div>
            <label for="${value.themeKey}">${value.title}</label>
            <textarea id="${value.themeKey}" data-menu-key="${menuKey}" name="${value.themeKey}" class="textarea">${configValues[fullKey] ?? value.defaultValue ?? ''}</textarea>
        </div>
        `;

            case 'image':
                return `
        <div class="drop-img-area" data-input-name="${value.themeKey}" data-menu-key="${menuKey}"></div>
        `;

            case 'boolean':
                return `
        <label for="${value.themeKey}" class="toggle">
            <p class="toggle-label">${value.title}</p>
            <input id="${value.themeKey}" data-menu-key="${menuKey}" name="${value.themeKey}" type="checkbox" class="toggle-input"
                ${(configValues[fullKey] === "1" || (configValues[fullKey] === undefined && value.defaultValue === "1")) ? "checked" : ""}>
            <div class="toggle-slider"></div>
        </label>
        `;

            case 'select':
                let optionsHtml = value.selectOptions.map(option => `
        <option value="${option.value}" ${(configValues[fullKey] ?? value.defaultValue) === option.value ? 'selected' : ''}>
            ${option.text}
        </option>
    `).join('');

                return `
        <div>
            <label for="${value.themeKey}">${value.title}</label>
            <select data-menu-key="${menuKey}" id="${value.themeKey}" name="${value.themeKey}" class="input">
                ${optionsHtml}
            </select>
        </div>
    `;

            default:
                return `
        <div>
            <label for="${value.themeKey}">${value.title}</label>
            <input type="text" data-menu-key="${menuKey}" id="${value.themeKey}" name="${value.themeKey}" class="input" placeholder="Default"
                value="${configValues[fullKey] ?? value.defaultValue ?? ''}">
        </div>
        `;
        }
    }


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

    document.addEventListener("DOMContentLoaded", () => {
        const iframe = document.getElementById("previewFrame");

        iframe.onload = function () {
            updateThemePreview();
        };

        function updateThemePreview(keyToUpdate = null) {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            if (!iframeDoc) return;

            // 🔹 Mise à jour des valeurs des commentaires textuels (ex: <!-- CMW:header:site_title -->)
            const iterator = iframeDoc.createNodeIterator(iframeDoc.body, NodeFilter.SHOW_COMMENT, {
                acceptNode: (node) => node.nodeValue.includes("CMW:") ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT
            });

            let currentNode;
            while ((currentNode = iterator.nextNode())) {
                const match = currentNode.nodeValue.trim().match(/CMW:([\w-]+):([\w-]+)/);
                if (match) {
                    const menuKey = match[1];
                    const valueKey = match[2];
                    const fullKey = `${menuKey}_${valueKey}`;

                    if (keyToUpdate && fullKey !== keyToUpdate) continue;

                    const themeValue = configValues[fullKey] || "";

                    let previousNode = currentNode.previousSibling;
                    if (previousNode && previousNode.nodeType === Node.TEXT_NODE) {
                        previousNode.textContent = themeValue;
                    } else {
                        const newElement = document.createTextNode(themeValue);
                        currentNode.parentNode.insertBefore(newElement, currentNode);
                    }
                }
            }

            // 🔹 Mise à jour des styles dynamiques et des attributs (ex: style="color: /* CMW:header:text_color */")
            iframeDoc.querySelectorAll("*").forEach(element => {
                Array.from(element.attributes).forEach(attr => {
                    if (attr.value.includes("/* CMW:")) {
                        const regex = /([\w#\d]+)?\s*\/\* CMW:([\w-]+):([\w-]+) \*\//g;
                        let newValue = attr.value.replace(regex, (match, oldValue, menuKey, valueKey) => {
                            const fullKey = `${menuKey}_${valueKey}`;
                            return (configValues[fullKey] || "").trim() + ` /* CMW:${menuKey}:${valueKey} */`;
                        });

                        newValue = newValue.replace(/(\S+)\s+\1/g, "$1"); // Supprime les doublons
                        element.setAttribute(attr.name, newValue.trim());
                    }
                });
            });
        }

        // 🔹 Écoute les modifications des inputs et met à jour uniquement l'élément concerné
        document.getElementById("sectionContent").addEventListener("input", (event) => {
            const menuKey = event.target.dataset.menuKey;
            const valueKey = event.target.name;
            if (!menuKey || !valueKey) return;

            const fullKey = `${menuKey}_${valueKey}`;
            configValues[fullKey] = event.target.value.trim();
            updateThemePreview(fullKey);
        });

        // 🔹 Gère aussi les cases à cocher
        document.getElementById("sectionContent").addEventListener("change", (event) => {
            if (event.target.type === "checkbox") {
                const menuKey = event.target.dataset.menuKey;
                const valueKey = event.target.name;
                if (!menuKey || !valueKey) return;

                const fullKey = `${menuKey}_${valueKey}`;
                configValues[fullKey] = event.target.checked ? "1" : "0";
                updateThemePreview(fullKey);
            }
        });
    });
</script>

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