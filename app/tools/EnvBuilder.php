<?php

namespace CMW\Utils;

use Closure;

class EnvBuilder
{

    private string $envFileName = ".env";
    private string $envPath;
    private string $path;
    private string $absPath;

    public function __construct()
    {
        $this->absPath = dirname(__DIR__, 2) . "/";
        $this->envPath = $this->absPath;
        $this->path = $this->envPath . $this->envFileName;

        if (!$this->checkForFile()) {
            $this->createFile();
        }

        $this->setDefaultValues();

        $this->load();

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

    private function valueExist($key): bool
    {
        $toReturn = false;

        $this->doWithFile(function ($name, $_) use ($key, &$toReturn) {
            if ($name === mb_strtoupper(trim($key))) {
                $toReturn = !$toReturn;
            }
        });

        return $toReturn;
    }

    public function setOrEditValue($key, $value): void
    {
        $this->valueExist($key) ? $this->editValue($key, $value) : $this->addValue($key, $value);
    }

    public function editValue($key, $value): void
    {
        if ($this->valueExist($key)) {
            $this->deleteValue($key);
            $this->addValue($key, $value);
        }
    }

    public function getValue($key): string|false
    {
        $toReturn = NULL;

        $this->doWithFile(function ($name, $value) use ($key, &$toReturn) {
            if ($name === mb_strtoupper(trim($key))) {
                $toReturn = $value;
            }
        });


        if ($this->valueExist($key)) {
            return $toReturn ?? $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        }

        return false;
    }

    public function deleteValue($key): void
    {
        if ($this->valueExist($key)) {
            $k = mb_strtoupper(trim($key));

            $buildLine = trim($k . "=" . $this->getValue($k)) . PHP_EOL;

            $contents = file_get_contents($this->path);
            $contents = str_replace($buildLine, '', $contents);
            file_put_contents($this->path, $contents);
            unset($_ENV[$k], $_SERVER[$k]);
            putenv($k);

            $this->load();
        }
    }

    public function addValue($key, $value): void
    {
        if (!$this->valueExist($key)) {
            $file = fopen($this->envPath . $this->envFileName, 'ab');
            $textToSet = static function ($key, $value) {
                return mb_strtoupper(trim($key)) . "=" . trim($value) . PHP_EOL;
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
            $k = mb_strtoupper(trim($name));

            if (!array_key_exists($k, $_SERVER) && !array_key_exists($k, $_ENV)) {
                putenv(sprintf('%s=%s', $k, $value));
                $_ENV[$k] = $value;
                $_SERVER[$k] = $value;
            }
        });
    }

    private function checkForFile(): bool
    {
        return is_file($this->envPath . $this->envFileName);
    }

    private function createFile(): void
    {
        fclose(fopen($this->envPath . $this->envFileName, "wb"));
    }

    private function setDefaultValues(): void
    {
        $this->addValue("installStep", 0);
        $this->addValue("dir", $this->absPath);
        $this->addValue("devMode", 0);
    }

}