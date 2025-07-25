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
use DOMDocument;
use DOMXPath;

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
        if ($editorMode) return $html;

        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);

        $this->processCmwVisible($xpath);
        $this->processCmwText($xpath);
        $this->processCmwStyle($xpath);
        $this->processCmwClass($xpath);
        $this->processCmwAttr($xpath);
        $this->processCmwVar($xpath);
        $this->processCmwStyleComments($xpath);

        return $dom->saveHTML();
    }

    /**
     * @param \DOMXPath $xpath
     * @return void
     */
    private function processCmwVisible(DOMXPath $xpath): void
    {
        $elements = $xpath->query('//*[@data-cmw-visible]');
        foreach ($elements as $element) {
            $data = $element->getAttribute('data-cmw-visible');

            if (preg_match('/^([\w-]+):([\w-]+)$/', $data, $matches)) {
                $menu = $matches[1];
                $key = $matches[2];
                $visible = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

                if (!$visible || $visible === '0') {
                    $element->parentNode?->removeChild($element);
                } else {
                    $element->removeAttribute('data-cmw-visible');
                }
            }
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @return void
     */
    private function processCmwText(DOMXPath $xpath): void
    {
        $nodes = $xpath->query('//*[@data-cmw]');
        foreach ($nodes as $node) {
            if (preg_match('/^([\w-]+):([\w-]+)$/', $node->getAttribute('data-cmw'), $m)) {
                $menu = $m[1];
                $key = $m[2];
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $editorType = ThemeConfigResolver::getInstance()->getEditorType($menu, $key);

                while ($node->firstChild) {
                    $node->removeChild($node->firstChild);
                }

                if ($editorType === EditorType::HTML) {
                    $tmpDoc = new DOMDocument();
                    $tmpDoc->loadHTML('<?xml encoding="utf-8" ?><div>' . $val . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                    foreach ($tmpDoc->documentElement->childNodes as $child) {
                        $node->appendChild($node->ownerDocument->importNode($child, true));
                    }
                } else {
                    $node->appendChild($node->ownerDocument->createTextNode($val));
                }

                $node->removeAttribute('data-cmw');
            }
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @return void
     */
    private function processCmwStyle(DOMXPath $xpath): void
    {
        $nodes = $xpath->query('//*[@data-cmw-style]');
        foreach ($nodes as $node) {
            $styleAttr = $node->getAttribute('style');
            $existingStyles = [];

            if ($styleAttr) {
                foreach (explode(';', $styleAttr) as $styleLine) {
                    if (strpos($styleLine, ':') !== false) {
                        [$k, $v] = explode(':', $styleLine, 2);
                        $existingStyles[trim($k)] = trim($v);
                    }
                }
            }

            $rules = explode(';', $node->getAttribute('data-cmw-style'));
            foreach ($rules as $rule) {
                if (count(explode(':', $rule)) === 3) {
                    [$prop, $menu, $key] = explode(':', $rule);

                    $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                    $editorType = ThemeConfigResolver::getInstance()->getEditorType($menu, $key);

                    if ($editorType === EditorType::RANGE) {
                        $opts = ThemeConfigResolver::getInstance()->getEditorRangeOptions($menu, $key);
                        $val = $opts->getPrefix() . $val . $opts->getSuffix();
                    }

                    if ($editorType === EditorType::IMAGE && in_array(trim($prop), ['background', 'background-image', 'list-style-image', 'mask-image'])) {
                        $val = "url('{$val}')";
                    }

                    $existingStyles[trim($prop)] = $val;
                }
            }

            $finalStyle = implode('; ', array_map(fn($k, $v) => "$k: $v", array_keys($existingStyles), $existingStyles));
            $node->setAttribute('style', $finalStyle);
            $node->removeAttribute('data-cmw-style');
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @return void
     */
    private function processCmwClass(DOMXPath $xpath): void
    {
        $nodes = $xpath->query('//*[@data-cmw-class]');
        foreach ($nodes as $node) {
            $refs = explode(' ', $node->getAttribute('data-cmw-class'));

            $dynamicClasses = [];
            foreach ($refs as $ref) {
                [$menu, $key] = explode(':', $ref);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

                $editorType = ThemeConfigResolver::getInstance()->getEditorType($menu, $key);
                if ($editorType === EditorType::RANGE) {
                    $opts = ThemeConfigResolver::getInstance()->getEditorRangeOptions($menu, $key);
                    if ($opts instanceof EditorRangeOptions) {
                        $val = $opts->getPrefix() . $val . $opts->getSuffix();
                    }
                }

                if ($val) {
                    $dynamicClasses[] = trim($val);
                }
            }

            $existingClasses = explode(' ', $node->getAttribute('class') ?? '');
            $finalClasses = array_filter(array_merge($existingClasses, $dynamicClasses));
            $node->setAttribute('class', implode(' ', $finalClasses));
            $node->removeAttribute('data-cmw-class');
        }
    }

    /**
     * @param \DOMXPath $xpath
     * @return void
     */
    private function processCmwAttr(DOMXPath $xpath): void
    {
        $nodes = $xpath->query('//*[@data-cmw-attr]');
        foreach ($nodes as $node) {
            $defs = explode(' ', $node->getAttribute('data-cmw-attr'));

            foreach ($defs as $def) {
                [$attr, $menu, $key] = explode(':', $def);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $node->setAttribute($attr, $val);
            }

            $node->removeAttribute('data-cmw-attr');
        }
    }

    private function processCmwVar(DOMXPath $xpath): void
    {
        $nodes = $xpath->query('//*[@data-cmw-var]');
        foreach ($nodes as $node) {
            $defs = explode(' ', $node->getAttribute('data-cmw-var'));

            foreach ($defs as $def) {
                [$varName, $menu, $key] = explode(':', $def);
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);
                $node->setAttribute('style', $node->getAttribute('style') . "; $varName: $val");
            }

            $node->removeAttribute('data-cmw-var');
        }
    }

    private function processCmwStyleComments(DOMXPath $xpath): void
    {
        $styleTags = $xpath->query('//style');

        foreach ($styleTags as $styleTag) {
            $css = $styleTag->textContent;

            $css = preg_replace_callback('/\/\*cmw:([\w-]+):([\w-]+)\*\//', function ($matches) {
                [$full, $menu, $key] = $matches;
                $val = ThemeModel::getInstance()->fetchConfigValue($menu, $key);

                $editorType = ThemeConfigResolver::getInstance()->getEditorType($menu, $key);

                if ($editorType === EditorType::RANGE) {
                    $opts = ThemeConfigResolver::getInstance()->getEditorRangeOptions($menu, $key);
                    $val = $opts->getPrefix() . $val . $opts->getSuffix();
                }

                return $val . " /*cmw:$menu:$key*/";
            }, $css);

            $styleTag->textContent = $css;
        }
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
    <input type="color" id="{$inputId}" name="{$inputName}" class="input" style="border: #bababa 1px solid" value="{$valEscaped}">
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