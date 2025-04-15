<?php

namespace CMW\Manager\Theme\Editor;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Config\ThemeConfigResolver;
use CMW\Manager\Theme\Editor\Entities\EditorMenu;
use CMW\Manager\Theme\Editor\Entities\EditorRangeOptions;
use CMW\Manager\Theme\Editor\Entities\EditorType;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Theme\ThemeManager;
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
}