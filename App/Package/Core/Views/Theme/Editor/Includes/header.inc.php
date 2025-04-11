<?php

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Interface\Core\ISideBarElements;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Notification\NotificationModel;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;

$currentUser = UsersSessionsController::getInstance()->getCurrentUser();
$sideBarImplementations = Loader::loadImplementations(ISideBarElements::class);
$notificationNumber = NotificationModel::getInstance()->countUnreadNotification();
$notifications = NotificationModel::getInstance()->getUnreadNotification();

/* @var \CMW\Manager\Theme\Editor\EditorMenu[] $themeMenus */
/* @var CoreController $coreAdmin */

?>
<nav class="nav">
    <div class="px-3 py-[.1rem] lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                        type="button" class="sm:hidden">
                    <i class="fa-solid fa-bars fa-lg"></i>
                </button>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/"
                   class="w-48 hidden sm:flex">
                    <img
                        src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/logo_dark.png"
                        class="bg-contain dark:hidden" alt="Logo"/>
                    <img
                        src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/logo.png"
                        class="bg-contain hidden dark:block" alt="Logo"/>
                </a>
                <div class="ml-2 sm:ml-11">
                    <button id="toggleSidebar" class="hidden sm:block"><i id="toggleIcon"
                                                                          class="fa-solid fa-expand fa-lg"></i></button>
                </div>
                <div class="ml-2">
                    <div class="px-3">
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_URL') ?>" target="_blank"><i
                                class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                </div>
                <?php CoreController::getInstance()->getPackagesTopBarElements(); ?>
            </div>
            <div class="flex gap-6">
                <button id="submitButton" form="ThemeSettings" type="submit" class="btn-success"><i class="fa-solid fa-cloud-arrow-up"></i> <?= LangManager::translate('core.btn.save') ?></button>
                <a data-modal-toggle="modal-reset" class="btn-warning cursor-pointer"><i class="fa-solid fa-rotate-left"></i> <?= LangManager::translate('core.theme.reset', ['theme' => ThemeManager::getInstance()->getCurrentTheme()->name()]) ?></a>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin" class="btn-danger"><i class="fa-solid fa-arrow-right-from-bracket"></i> <?= LangManager::translate('core.theme.leave') ?></a>
            </div>
            <div class="flex items-center">
                <div>
                    <button type="button" class="relative  p-2.5" data-dropdown-toggle="dropdown-notification">
                        <i class="fa-solid fa-bell fa-lg"></i>
                        <?php if ($notificationNumber): ?>
                            <div
                                class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full top-0 dark:border-gray-900"
                                style="right: -0.3rem;"><?= $notificationNumber ?></div>
                        <?php endif; ?>
                    </button>
                </div>
                <div style="width: 20rem; padding: 16px; max-height: 800px"
                     class="z-50 hidden space-y-2 overflow-x-auto border rounded-l bg-white dark:bg-gray-800"
                     id="dropdown-notification">
                    <?php
                    $max_notifications = 3;
                    $notification_count = 0;
                    foreach ($notifications as $notification):
                        if ($notification_count >= $max_notifications) {
                            break;
                        }
                        $notification_count++;
                        ?>
                        <div>
                            <div class="rounded-lg border bg-white dark:bg-gray-700 dark:border-gray-700">
                                <div class="flex justify-between p-3">
                                    <p><b><?= $notification->getPackage() ?></b><small>
                                            - <?= mb_strimwidth($notification->getTitle(), 0, 30, '...') ?></small></p>
                                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/read/<?= $notification->getId() ?>"
                                       class="ms-auto -mx-1.5 -my-1.5 bg-white justify-center items-center flex-shrink-0 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                             fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="border-t p-3">
                                    <p><?= mb_strimwidth($notification->getMessage(), 0, 70, '...') ?></p>
                                </div>
                                <?php if ($notification->getSlug()): ?>
                                    <div class="border-t p-2 flex justify-end">
                                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/goTo/<?= $notification->getId() ?>"
                                           class="btn-primary-sm">S'y rendre</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                    endforeach;
                    ?>
                    <div class="rounded-lg border bg-white dark:bg-gray-700 dark:border-gray-700 text-center p-2">
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notifications"
                           class="link">
                            Voir toutes les notifications
                        </a>
                    </div>
                </div>
                <div>
                    <button id="theme-toggle" type="button" class="p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-gray-800" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-yellow-500" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center ml-2">
                    <div>
                        <button type="button" class="flex rounded-full" data-dropdown-toggle="dropdown-user">
                            <img class="w-8 h-8 rounded-full" src="<?= $currentUser->getUserPicture()->getImage() ?>"
                                 alt="user">
                        </button>
                    </div>
                    <div
                        class="min-w-[10rem] z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                        id="dropdown-user">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900 dark:text-white" role="none">
                                <?= $currentUser->getPseudo() ?>
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                <?= $currentUser->getHighestRole()->getName() ?>
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600"
                                   role="menuitem"
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/users/manage/edit/' . $currentUser->getId() ?>">
                                    <i class="fa-solid fa-user"></i>
                                    <?= LangManager::translate('users.users.link_profile') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>logout"
                                   class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600"
                                   role="menuitem"><i
                                        class="fa-solid fa-right-from-bracket"></i> <?= LangManager::translate('users.users.logout') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<aside id="logo-sidebar" class="aside-nav" aria-label="Sidebar">
    <div class="flex justify-center gap-6 py-2">
        <button class="mode-btn" onclick="setIframeWidth('mobile', this)">
            <i class="fa-solid fa-mobile-screen-button"></i>
        </button>
        <button class="mode-btn" onclick="setIframeWidth('tablet', this)">
            <i class="fa-solid fa-tablet-screen-button"></i>
        </button>
        <button class="mode-btn active" onclick="setIframeWidth('desktop', this)">
            <i class="fa-solid fa-desktop"></i>
        </button>
    </div>
    <hr class="mt-0 mb-0">
    <p class="text-center text-lg mt-2 font-bold"><?= LangManager::translate('core.theme.menu') ?></p>
    <form id="ThemeSettings" action="/cmw-admin/theme/manage" method="post" enctype="multipart/form-data">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div id="menuSections" style="max-height: calc(100vh - 7.3rem);" class="overflow-y-auto overflow-x-hidden">
            <ul style="border-bottom: 1px dashed #b5a5a5">
                <?php foreach ($themeMenus as $index => $package): ?>
                    <li>
                        <button type="button"
                            class="text-info w-full p-2 hover:bg-gray-200 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" style="text-align: left; border-top: 1px dashed #b5a5a5"
                            onclick="showSection(<?= $index ?>)"
                                data-title="<?= htmlspecialchars($package->title) ?>"
                                data-values='<?= json_encode(array_map(fn($v) => [
                                    'title' => $v->title,
                                    'themeKey' => $v->themeKey,
                                    'defaultValue' => $v->defaultValue,
                                    'type' => $v->type,
                                    'selectOptions' => $v->selectOptions ?? [],
                                ], $package->values), JSON_THROW_ON_ERROR) ?>'
                                data-scope="<?= $package->getScope() ?>"
                                data-menukey="<?= $package->getMenuKey() ?>"
                        >
                            <i class="fa-regular fa-circle-right"></i> <?= htmlspecialchars($package->title) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Section d'édition -->
        <div id="editorSection" class="hidden">
            <button type="button" onclick="backToMenu()" class="text-info mb-2 w-full p-2 hover:bg-gray-200 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600" style="text-align: left; border-bottom: 1px dashed #b5a5a5; border-top: 1px dashed #b5a5a5">
                <i class="fa-regular fa-circle-left"></i> Retour au menu
            </button>
            <p id="sectionTitle" class="text-center text-lg"></p>
            <div id="sectionContent" class="mt-3 space-y-3 px-1"></div>
        </div>
        <div id="allSections">
            <?php foreach ($themeMenus as $index => $package): ?>
                <div class="theme-section hidden overflow-y-auto overflow-x-hidden px-1" style="max-height: calc(100vh - 13.3rem);" id="section_<?= $package->getMenuKey() ?>"
                     data-scope="<?= $package->getScope() ?>">
                    <?php foreach ($package->values as $value): ?>
                    <div style="padding: 1rem 0 1rem 0; border-top: dashed 2px #b5a5a5">
                        <?= renderInput($value, $package->getMenuKey(), $formattedConfigs[$package->getMenuKey().'_'.$value->themeKey] ?? $value->defaultValue ?? '') ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </form>
</aside>

<?php
//TODO Ajouter des truc colle comme le fa picker, et peut être d'autres trucs
function renderInput($value, $menuKey, $val)
{
    $inputName = $menuKey . '_' . $value->themeKey;
    $inputId = htmlspecialchars($value->themeKey);
    $label = htmlspecialchars($value->title);
    $valEscaped = htmlspecialchars($val);

    switch ($value->type) {
        case 'color':
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <input type="color" id="{$inputId}" name="{$inputName}" class="input" value="{$valEscaped}">
HTML;

        case 'number':
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <input type="number" id="{$inputId}" name="{$inputName}" class="input" value="{$valEscaped}">
HTML;

        case 'text':
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <input type="text" id="{$inputId}" name="{$inputName}" class="input" value="{$valEscaped}" placeholder="Default">
HTML;

        case 'faPicker':
            return <<<HTML
<div class="icon-picker" data-id="for-{$inputId}" data-label="{$label}" data-name="{$inputName}" data-placeholder="Sélectionner un icon" data-value="{$valEscaped}"></div>
HTML;

        case 'textarea':
        case 'css':
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <textarea id="{$inputId}" name="{$inputName}" class="textarea">{$valEscaped}</textarea>
HTML;

        case 'boolean':
            $checked = ($val === "1" || ($val === null && $value->defaultValue === "1")) ? "checked" : "";
            return <<<HTML
    <label for="{$inputId}" class="toggle">
        <p class="toggle-label">{$label}</p>
        <input id="{$inputId}" name="{$inputName}" type="checkbox" class="toggle-input" {$checked}>
        <div class="toggle-slider"></div>
    </label>
HTML;

        case 'select':
            $optionsHtml = '';
            foreach ($value->selectOptions ?? [] as $option) {
                $selected = ($val === $option->value || ($val === null && $value->defaultValue === $option->value)) ? 'selected' : '';
                $optVal = htmlspecialchars($option->value);
                $optText = htmlspecialchars($option->text);
                $optionsHtml .= "<option value=\"{$optVal}\" {$selected}>{$optText}</option>";
            }
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <select id="{$inputId}" name="{$inputName}" class="input">{$optionsHtml}</select>
HTML;

        case 'image':
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <input id="{$inputId}" name="{$inputName}" type="file" value="{$valEscaped}">
HTML;

        case 'range':
            $range = $value->rangeOptions[0] ?? null;

            if (!$range) {
                return ''; // si mal configuré
            }

            $min = $range->getMin();
            $max = $range->getMax();
            $step = $range->getStep();
            $prefix = htmlspecialchars($range->getPrefix());
            $suffix = htmlspecialchars($range->getSuffix());

            return <<<HTML
    <label for="{$inputId}">{$label} (<small id="preview_{$inputId}">{$prefix}{$valEscaped}{$suffix}</small>)</label>
    
    <div class="flex items-center gap-2">
        <input type="range" 
               id="{$inputId}" 
               name="{$inputName}" 
               min="{$min}" 
               max="{$max}" 
               step="{$step}" 
               value="{$valEscaped}" 
               class="w-full"
               oninput="document.getElementById('preview_{$inputId}').innerText = '{$prefix}' + this.value + '{$suffix}'">
    </div>
HTML;


        default:
            return <<<HTML
    <label for="{$inputId}">{$label}</label>
    <input type="text" id="{$inputId}" name="{$inputName}" class="input" value="{$valEscaped}" placeholder="Default">
HTML;
    }
}

?>

<script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
        document.body.classList.toggle('sidebar-collapsed');

        // Récupérer l'élément de l'icône
        let icon = document.getElementById('toggleIcon');

        // Ajouter la classe flip pour l'effet de rotation
        icon.classList.add('flip');

        // Vérifier si la sidebar est masquée et basculer l'icône
        setTimeout(function () {
            if (document.body.classList.contains('sidebar-collapsed')) {
                icon.classList.remove('fa-expand', 'fa-lg');
                icon.classList.add('fa-bars', 'fa-lg');
            } else {
                icon.classList.remove('fa-bars', 'fa-lg');
                icon.classList.add('fa-expand', 'fa-lg');
            }

            // Retirer l'effet de flip après le changement
            icon.classList.remove('flip');
        }, 300); // Attendez un peu pour l'effet avant de changer l'icône
    });

    function toggleSubMenu(button) {
        const submenu = button.nextElementSibling;

        if (submenu.classList.contains('active')) {
            submenu.style.maxHeight = null;  // Réinitialiser la hauteur à 0
            submenu.classList.remove('active');
        } else {
            submenu.classList.add('active');
            submenu.style.maxHeight = (submenu.scrollHeight + 16) + "px";  // Ajuste dynamiquement la hauteur
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const openActiveMenus = () => {
            document.querySelectorAll('.a-side-nav-dropdown ul').forEach(subMenu => {
                const activeItem = subMenu.querySelector('.side-nav-drop-active');
                if (activeItem) {
                    subMenu.classList.add('open');

                    const parentMenu = subMenu.previousElementSibling;
                    if (parentMenu && parentMenu.querySelector('.fa-chevron-down')) {
                        parentMenu.querySelector('.fa-chevron-down').classList.add('rotate-180');
                    }
                }
            });
        };

        document.querySelectorAll('.a-side-nav-drop-sub').forEach(menu => {
            menu.addEventListener('click', (event) => {
                event.stopPropagation();

                const subMenu = menu.nextElementSibling;

                if (subMenu && subMenu.tagName.toLowerCase() === 'ul') {
                    const isOpen = subMenu.classList.contains('open');
                    if (isOpen) {
                        subMenu.classList.remove('open');
                    } else {
                        subMenu.classList.add('open');
                    }

                    const chevron = menu.querySelector('.fa-chevron-down');
                    if (chevron) {
                        chevron.classList.toggle('rotate-180', !isOpen);
                    }
                }
            });
        });

        openActiveMenus();
    });


    document.addEventListener("DOMContentLoaded", function () {
        let dropActiveElement = document.querySelector(".side-nav-drop-active");
        let activeElement = document.querySelector(".side-nav-active");
        if (dropActiveElement) {
            dropActiveElement.scrollIntoView({block: "center"});
        } else if (activeElement) {
            activeElement.scrollIntoView({block: "center"});
        }
    });

</script>

<section class="main-content">

    <div id="modal-reset" class="modal-container">
        <div class="modal">
            <div class="modal-header-warning">
                <h6>Réinitialisation</h6>
                <button type="button" data-modal-hide="modal-reset"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                Ceci va remettre TOUTES les valeurs par défaut du thème !
            </div>
            <div class="modal-footer">
                <a href="market/regenerate" class="btn-warning">Réinitialiser</a>
            </div>
        </div>
    </div>