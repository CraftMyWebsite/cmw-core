<?php

namespace CMW\Manager\Lang;

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Loader\Loader;

class LangManager
{
    private static array $translations;
    private static array $langCache = [];

    private const string CANNOT_TRANSLATE = 'NO TRANSLATION FOUND FOR %value%';

    private static function setTranslationList(array $array, string $package, string $lang): void
    {
        if (isset(self::$langCache[$package])) {
            self::$langCache[$package] = [];
        }
        self::$langCache[$package][$lang] = $array;
    }

    private static function getTranslationSentence(array $sentenceElement, array $translationList): string|array|null
    {
        while (count($sentenceElement) !== 0) {
            $value = array_shift($sentenceElement);

            if (!is_array($translationList) || !array_key_exists($value, $translationList)) {
                return null;
            }

            $translationList = $translationList[$value];
        }

        return $translationList;
    }

    private static function parseVariables(string $translation, array $vars): string
    {
        foreach ($vars as $key => $var) {
            $key = strtolower($key);
            if (!is_null($var)) {
                $translation = str_replace("%$key%", $var, $translation);
            }
        }

        return $translation;
    }

    public static function getTranslationList(string $package, string $lang): array|string|null
    {
        return self::$langCache[$package][$lang] ?? null;
    }

    public static function loadTranslation(string $package, ?string $lang = null): ?array
    {
        if (is_null($lang)) {
            $lang = strtolower(EnvManager::getInstance()->getValue('LOCALE'));
        }

        if (is_null(self::getTranslationList($package, $lang))) {
            if ($package === 'Installation') {
                $translationList = InstallerController::loadLang();
            } else {
                $translationList = Loader::loadLang($package, $lang);
            }

            if (is_null($translationList)) {
                return null;
            }

            self::setTranslationList($translationList, $package, $lang);
        }

        return self::getTranslationList($package, $lang);
    }

    public static function translate(string $valueToTranslate, array $vars = [], bool $lineBreak = false, bool $forJs = false): string
    {
        $CANNOT_TRANSLATE = strtoupper(str_replace('%value%', "<b>$valueToTranslate</b>", self::CANNOT_TRANSLATE));

        if (count(explode('.', $valueToTranslate)) < 2) {
            return $CANNOT_TRANSLATE;
        }

        $elements = explode('.', $valueToTranslate);

        $package = array_shift($elements);

        $translations = self::loadTranslation($package);
        if (is_null($translations)) {
            return $CANNOT_TRANSLATE;
        }

        $translation = self::getTranslationSentence($elements, $translations);

        if (!is_string($translation)) {
            return $CANNOT_TRANSLATE;
        }

        $toReturn = self::parseVariables($translation, $vars) . ($lineBreak ? '<br>' : '');

        if ($forJs) {
            $toReturn = '"' . $toReturn . '"';
        }

        return $toReturn;
    }
}
