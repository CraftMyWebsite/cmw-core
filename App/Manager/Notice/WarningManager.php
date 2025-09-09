<?php
namespace CMW\Manager\Notice;

/**
 * desc Please note, this only works to notify the admin of errors from packages and themes, and is displayed on all pages!
 */
final class WarningManager
{
    private const SESSION_KEY = 'cmw_notices';

    /**
     * @return void
     */
    private static function boot(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) { @session_start(); }
        if (!isset($_SESSION[self::SESSION_KEY])) {
            // on stocke des maps clé => message (pas des listes)
            $_SESSION[self::SESSION_KEY] = [
                'info'    => [],
                'warning' => [],
                'error'   => [],
            ];
        }
    }

    /** @param string|null $key clé de dédup (par défaut: hash du message) */
    public static function addWarning(string $message, ?string $key = null): void
    {
        self::boot();
        $key ??= sha1($message);
        $_SESSION[self::SESSION_KEY]['warning'][$key] = $message; // overwrite = idempotent
    }

    /**
     * @param string $message
     * @param string|null $key
     * @return void
     */
    public static function addInfo(string $message, ?string $key = null): void
    {
        self::boot();
        $key ??= sha1($message);
        $_SESSION[self::SESSION_KEY]['info'][$key] = $message;
    }

    /**
     * @param string $message
     * @param string|null $key
     * @return void
     */
    public static function addError(string $message, ?string $key = null): void
    {
        self::boot();
        $key ??= sha1($message);
        $_SESSION[self::SESSION_KEY]['error'][$key] = $message;
    }

    /**
     * @return array{info:string[],warning:string[],error:string[]}
     */
    public static function pullAll(): array
    {
        self::boot();
        $all = [
            'info'    => array_values($_SESSION[self::SESSION_KEY]['info']),
            'warning' => array_values($_SESSION[self::SESSION_KEY]['warning']),
            'error'   => array_values($_SESSION[self::SESSION_KEY]['error']),
        ];
        // reset
        $_SESSION[self::SESSION_KEY] = ['info'=>[], 'warning'=>[], 'error'=>[]];
        return $all;
    }

    /**
     * @return bool
     */
    public static function hasNotices(): bool
    {
        self::boot();
        foreach ($_SESSION[self::SESSION_KEY] as $map) {
            if (!empty($map)) return true;
        }
        return false;
    }
}
