<?php

namespace CMW\Manager\Theme\Editor;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Config\ThemeConfigResolver;
use CMW\Manager\Theme\Config\ThemeMapper;
use CMW\Manager\Theme\Editor\Entities\EditorMenu;
use CMW\Manager\Theme\Editor\Entities\EditorRangeOptions;
use CMW\Manager\Theme\Editor\Entities\EditorType;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Model\Core\ThemeModel;

class ThemeEditorProcessor extends AbstractManager
{
    /**
     * @param string $html
     * @param bool $editorMode
     * @return string
     * @description Replace the Theme config in Public view for final render
     */
    public function replaceThemeValues(string $html, bool $editorMode = false): string
    {
        if ($editorMode) {
            return $html;
        }

        // data-cmw="menu:key"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw="([\w-]+):([\w-]+)"([^>]*)>(.*?)<\/\1>/si', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $menu = $m[3];
            $key = $m[4];
            $after = $m[5];

            $value = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

            return "<{$tag}{$before}{$after}>{$value}</{$tag}>";
        }, $html);


        // data-cmw-style="prop:menu:key[;...]"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw-style="([^"]+)"([^>]*)>/i', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $cmwAttr = $m[3];
            $after = $m[4];

            preg_match('/style="([^"]*)"/i', $before . $after, $existingStyleMatch);
            $existingStyles = [];

            if (isset($existingStyleMatch[1])) {
                foreach (explode(';', $existingStyleMatch[1]) as $styleLine) {
                    if (strpos($styleLine, ':') !== false) {
                        [$k, $v] = explode(':', $styleLine, 2);
                        $existingStyles[trim($k)] = trim($v);
                    }
                }
            }

            $styles = explode(';', $cmwAttr);
            foreach ($styles as $entry) {
                [$prop, $menu, $key] = explode(':', $entry);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeConfigResolver::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $options = ThemeConfigResolver::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($options instanceof EditorRangeOptions) {
                        $val = $options->getPrefix() . $val . $options->getSuffix();
                    }
                }

                // pour les images utilisées dans des styles CSS
                $imageStyleProps = ['background', 'background-image', 'list-style-image', 'mask-image'];
                if ($editorType === EditorType::IMAGE && in_array(trim($prop), $imageStyleProps)) {
                    $val = "url('{$val}')";
                }

                $existingStyles[trim($prop)] = $val;
            }

            $cleaned = preg_replace('/style="[^"]*"/i', '', $before . $after);
            $finalStyle = implode('; ', array_map(fn($k, $v) => "$k: $v", array_keys($existingStyles), $existingStyles));

            return "<{$tag} {$cleaned}style=\"{$finalStyle}\">";
        }, $html);


        // data-cmw-class="menu:key [...]"
        $html = preg_replace_callback('/<([a-z0-9]+)([^>]*)data-cmw-class="([^"]+)"([^>]*)>/i', function ($m) {
            $tag = $m[1];
            $before = $m[2];
            $cmwAttr = $m[3];
            $after = $m[4];

            // Récupérer les classes déjà présentes (dans before ou after)
            preg_match('/class="([^"]*)"/i', $before . $after, $existingClassMatch);
            $existingClasses = isset($existingClassMatch[1]) ? explode(' ', $existingClassMatch[1]) : [];

            $refs = explode(' ', $cmwAttr);
            $dynamicClasses = [];

            foreach ($refs as $ref) {
                [$menu, $key] = explode(':', $ref);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeConfigResolver::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $options = ThemeConfigResolver::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($options instanceof EditorRangeOptions) {
                        $val = $options->getPrefix() . $val . $options->getSuffix();
                    }
                }

                if ($val) {
                    $dynamicClasses[] = $val;
                }
            }

            // Supprimer l'ancien class="..." du before et after
            $cleaned = preg_replace('/class="[^"]*"/i', '', $before . $after);

            // Fusion et reconstruction
            $finalClasses = array_filter(array_merge($existingClasses, $dynamicClasses));
            return "<{$tag} {$cleaned}class=\"" . implode(' ', $finalClasses) . "\">";
        }, $html);


        // data-cmw-visible="menu:key" → suppression de l’élément si valeur = 0
        $html = preg_replace_callback('/<([a-z]+)([^>]+)data-cmw-visible="([\w-]+):([\w-]+)"([^>]*)>(.*?)<\/\1>/si', function ($m) {
            $visible = ThemeModel::getInstance()->fetchConfigValue($m[3], $m[4]);
            if (!$visible || $visible === '0') {
                return ''; // supprimer l’élément entier
            }

            return "<{$m[1]}{$m[2]}{$m[5]}>{$m[6]}</{$m[1]}>";
        }, $html);

        // data-cmw-attr="attr:menu:key [...]"
        $html = preg_replace_callback('/data-cmw-attr="([^"]+)"/', function ($m) {
            $defs = explode(' ', $m[1]);
            $attrs = [];

            foreach ($defs as $def) {
                [$attr, $menu, $key] = explode(':', $def);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

                $attrs[] = "{$attr}=\"{$val}\"";
            }

            return implode(' ', $attrs);
        }, $html);

        return $html;
    }

    /**
     * @return EditorMenu[]
     */
    public function getThemeMenus(): array
    {
        $themeName = ThemeLoader::getInstance()->getCurrentTheme()->name();

        $configPath = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$themeName/Config/config.settings.php";

        if (!file_exists($configPath)) {
            return [];
        }

        $menus = include $configPath;

        return array_filter($menus, function ($menu) {
            return !isset($menu->requiredPackage) || PackageController::isInstalled($menu->requiredPackage);
        });
    }

    /**
     * @param $value
     * @param $menuKey
     * @param $val
     * @return string
     * @description render input by EditorType in builder menu
     */
    public function renderInput($value, $menuKey, $val) :string
    {
        $inputName = ThemeMapper::mapConfigKey($menuKey, $value->themeKey);
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

            case 'html':
                return <<<HTML
<label for="{$inputId}">{$label}</label>
<div class="border rounded-lg html-editor-container" style="height: 15vh;" data-name="{$inputName}">
    <div id="editor-{$inputId}" class="html-editor">{$valEscaped}</div>
</div>
<input type="hidden" name="{$inputName}" id="input-{$inputId}" value="{$valEscaped}">
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
}