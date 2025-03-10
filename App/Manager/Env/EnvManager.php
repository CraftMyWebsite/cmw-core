<?php

namespace CMW\Manager\Env;

use CMW\Manager\Error\ErrorManager;
use CMW\Utils\Utils;
use Closure;
use Exception;

use function is_readable;
use function is_writable;

/**
 * Class: @EnvManager
 * @package Utils
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class EnvManager
{
    private static EnvManager $_instance;

    private string $envFileName = '.env';
    private string $envPath;
    private string $path;
    private string $absPath;
    private string $apiURL;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->absPath = dirname(__DIR__, 3) . '/';
        $this->envPath = $this->absPath;
        $this->path = $this->envPath . $this->envFileName;
        $this->apiURL = 'https://apiv2.craftmywebsite.fr';

        if (!$this->checkForFile()) {
            $this->createFile();
        }

        if (!$this->hasReadPerms()) {
            ErrorManager::showCustomErrorPage(
                'Permission error:',
                'The file .env is not readable. Please check the permissions of the file.',
            );
        }

        $this->setDefaultValues();
        $this->oneShotCmwVersionFile();

        $this->load();
    }

    public function __get(string $key)
    {
        return $this->getValue($key);
    }

    public function __set(string $key, ?string $value)
    {
        $this->setOrEditValue($key, $value);
    }

    public function __isset(string $key)
    {
        return $this->valueExist($key);
    }

    private function doWithFile(Closure $fn): void
    {
        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!count($lines)) {
            return;
        }

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            $fn($name, $value);
        }
    }

    public static function getInstance(): EnvManager
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function valueExist(string $key): bool
    {
        $key = mb_strtoupper(trim($key));
        return isset($_ENV[$key]);
    }

    public function valueExistInFile(string $key): bool
    {
        $toReturn = false;

        $key = mb_strtoupper(trim($key));

        $this->doWithFile(function ($name, $_) use ($key, &$toReturn) {
            if ($name === $key) {
                $toReturn = !$toReturn;
            }
        });

        return $toReturn;
    }

    public function setOrEditValue(string $key, ?string $value): void
    {
        $key = mb_strtoupper(trim($key));
        $this->valueExist($key) ? $this->editValue($key, $value) : $this->addValue($key, $value);
    }

    public function editValue(string $key, ?string $value): void
    {
        $key = mb_strtoupper(trim($key));

        if ($this->valueExist($key)) {
            $this->deleteValue($key);
            $this->addValue($key, $value);
        }
    }

    public function getValue(string $key): ?string
    {
        $key = mb_strtoupper(trim($key));

        if (!$this->valueExist($key)) {
            return null;
        }

        return $_ENV[$key];
    }

    public function deleteValue(string $key): void
    {
        $key = mb_strtoupper(trim($key));

        if ($this->valueExist($key)) {
            $buildLine = trim($key . '=' . $this->getValue($key)) . PHP_EOL;

            $contents = file_get_contents($this->path);
            $contents = str_replace($buildLine, '', $contents);
            file_put_contents($this->path, $contents);
            unset($_ENV[$key], $_SERVER[$key]);
            putenv($key);

            $this->load();
        }
    }

    public function addValue(string $key, ?string $value): void
    {
        if (!$this->hasWritePerms()) {
            ErrorManager::showCustomErrorPage(
                'Permission error.',
                'The file .env is not writable. Please check the permissions of the file.',
            );
        }

        $key = mb_strtoupper(trim($key));

        if (!$this->valueExistInFile($key)) {
            $file = fopen($this->envPath . $this->envFileName, 'ab');
            $textToSet = static function (string $key, ?string $value) {
                return $key . '=' . trim($value ?? 'UNDEFINED') . PHP_EOL;
            };

            $res = $textToSet($key, $value);
            fwrite($file, $res);

            fclose($file);

            $this->load();
        }
    }

    public function load(): void
    {
        $this->doWithFile(static function ($name, $value) {
            $key = mb_strtoupper(trim($name));

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        });
    }

    private function checkForFile(): bool
    {
        return is_file($this->envPath . $this->envFileName);
    }

    private function hasReadPerms(): bool
    {
        return is_readable($this->path);
    }

    private function hasWritePerms(): bool
    {
        return is_writable($this->path);
    }

    private function createFile(): void
    {
        fclose(fopen($this->envPath . $this->envFileName, 'wb'));
    }

    private function setDefaultValues(): void
    {
        $this->addValue('installStep', 0);
        $this->addValue('dir', $this->absPath);
        $this->addValue('devMode', 0);
        $this->addValue('APIURL', $this->apiURL);
        $this->generateSalt();
    }

    /**
     * @desc Generate a random string for salt if salt not exist
     */
    private function generateSalt(): void
    {
        if ($this->valueExist('SALT') && $this->valueExist('SALT_PASS') && $this->valueExist('SALT_IV')) {
            return;
        }

        require_once $this->absPath . 'App/Utils/Utils.php';

        try {
            $salt = Utils::genId(random_int(15, 45));
        } catch (Exception) {
            $salt = Utils::genId(25);
        }

        try {
            $saltPass = Utils::genId(random_int(15, 45));
        } catch (Exception) {
            $saltPass = Utils::genId(25);
        }

        $this->addValue('SALT', $salt);
        $this->addValue('SALT_PASS', $saltPass);
        $this->addValue('SALT_IV', bin2hex(openssl_random_pseudo_bytes(16)));
    }

    private function oneShotCmwVersionFile(): void
    {
        if ($this->getValue('VERSION') !== null || !file_exists('.cmw-version')) {
            return;
        }

        $data = file_get_contents('.cmw-version');
        $this->addValue('VERSION', $data);

        unlink('.cmw-version');
    }
}
