<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate('core.Theme.manage.title', ['Theme' => ThemeManager::getInstance()->getCurrentTheme()->name()]));
Website::setDescription(LangManager::translate('core.Theme.manage.description'));
?>

<div class="page-title">
    <h3><i class="fa-solid fa-palette"></i> <?= LangManager::translate('core.Theme.appearance') ?><b><?= ThemeManager::getInstance()->getCurrentTheme()->name() ?></b></h3>
    <div class="flex gap-2">
        <button data-modal-toggle="modal-danger" class="btn-warning" type="button"><?= LangManager::translate('core.Theme.reset') ?></button>
        <div>
            <button id="submitButton" form="ThemeSettings" type="submit" class="btn-primary">
                <?= LangManager::translate('core.btn.save') ?>
            </button>
        </div>
    </div>
</div>

<div class="page-loader">
    <form id="ThemeSettings" action="/cmw-admin/theme/manage" method="post" enctype="multipart/form-data">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div class="card">
            <?php ThemeManager::getInstance()->getCurrentThemeConfigFile(); ?>
        </div>
    </form>
</div>


<!--MODAL DANGER-->
<div id="modal-danger" class="modal-container">
    <div class="modal">
        <div class="modal-header-warning">
            <h6><?= LangManager::translate('core.Theme.reset') ?> ?</h6>
            <button type="button" data-modal-hide="modal-danger"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <?= LangManager::translate('core.Theme.verificationText') ?>
        </div>
        <div class="modal-footer">
            <a href="market/regenerate" type="submit" class="btn-warning">
                <?= LangManager::translate('core.Theme.reset') ?>
            </a>
        </div>
    </div>
</div>

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