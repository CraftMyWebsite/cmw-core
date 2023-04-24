<?php

namespace CMW\Manager\Class;

class ClassManager
{


    public static function getClassFullNameFromFile($filePathName): string
    {
        return self::getClassNamespaceFromFile($filePathName) . "\\" . self::getClassNameFromFile($filePathName);
    }


    public static function getClassObjectFromFile($filePathName)
    {
        $classString = self::getClassFullNameFromFile($filePathName);

        return new $classString;
    }

    protected static function getClassNamespaceFromFile($filePathName): ?string
    {
        $fileContent = file_get_contents($filePathName);

        $tokens = token_get_all($fileContent);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $hasNamespace = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $hasNamespace = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }

        return !$hasNamespace ? null : $namespace;
    }

    protected static function getClassNameFromFile($filePathName)
    {
        $php_code = file_get_contents($filePathName);

        $classes = array();
        $tokens = token_get_all($php_code);
        $count = count($tokens);
        for ($i = 2; $i < $count; $i++) {
            if ($tokens[$i - 2][0] === T_CLASS
                && $tokens[$i - 1][0] === T_WHITESPACE
                && $tokens[$i][0] === T_STRING
            ) {

                $className = $tokens[$i][1];
                $classes[] = $className;
            }
        }

        return $classes[0];
    }

}