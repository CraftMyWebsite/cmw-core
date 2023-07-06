<?php

namespace CMW\Manager\Security;


use CMW\Model\Users\UsersModel;
use Error;
use Exception;

class SecurityManager extends HoneyInput
{

    private string $formTokenLabel = 'security-csrf-token';

    private string $sessionTokenLabel;

    private array $post = [];

    private array $session = [];

    private array $server = [];

    private mixed $excludeUrl = [];

    private string $hashAlgo = 'sha256';

    private bool $hmac_ip = true;

    private string $hmacData = 'ABCeNBHVe3kmAqvU2s7yyuJSF2gpxKLC';

    public function __construct($excludeUrl = null, &$post = null, &$session = null, &$server = null)
    {
        $this->sessionTokenLabel = 'CSRF_TOKEN_SESS_ID_' . UsersModel::getCurrentUser()?->getId();

        if (!is_null($excludeUrl)) {
            $this->excludeUrl = $excludeUrl;
        }
        if (!is_null($post)) {
            $this->post = &$post;
        } else {
            $this->post = &$_POST;
        }

        if (!is_null($server)) {
            $this->server = &$server;
        } else {
            $this->server = &$_SERVER;
        }

        if (!is_null($session)) {
            $this->session = &$session;
        } elseif (isset($_SESSION)) {
            $this->session = &$_SESSION;
        } else {
            throw new Error('No session available for persistence');
        }
    }

    public function insertHiddenToken(): void
    {
        $csrfToken = $this->getCSRFToken();

        echo "<input type=\"hidden\"" . " name=\"" . $this->xssafe($this->formTokenLabel) . "\"" . " value=\"" . $this->xssafe($csrfToken) . "\"" . " />";

        $this->generateHoneyInput();
    }

    public function getCSRFToken()
    {
        if (empty($this->session[$this->sessionTokenLabel])) {
            try {
                $this->session[$this->sessionTokenLabel] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                $this->session[$this->sessionTokenLabel] = bin2hex(microtime() + $e);
            }
        }

        if ($this->hmac_ip !== false) {
            $token = $this->hMacWithIp($this->session[$this->sessionTokenLabel]);
        } else {
            $token = $this->session[$this->sessionTokenLabel];
        }
        return $token;
    }

    private function hMacWithIp($token): string
    {
        return hash_hmac($this->hashAlgo, $this->hmacData, $token);
    }

    public function xssafe($data, $encoding = 'UTF-8'): string
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    public function validate(): bool
    {
        $currentUrl = $this->getCurrentRequestUrl();
        if (!empty($this->post) && !in_array($currentUrl, $this->excludeUrl, true)) {
            if (!$this->validateRequest()) {
                return false;
            }
            if (!$this->checkHoneyInput()) {
                return false;
            }
            return true;
        }
        return false;
    }

    private function getCurrentRequestUrl(): string
    {
        $protocol = "http";
        if (isset($this->server['HTTPS'])) {
            $protocol = "https";
        }
        return $protocol . "://" . $this->server['HTTP_HOST'] . $this->server['REQUEST_URI'];
    }

    public function validateRequest(): bool
    {
        if (!isset($this->session[$this->sessionTokenLabel])) {
            // CSRF Token not found
            return false;
        }

        if (!empty($this->post[$this->formTokenLabel])) {
            // Let's pull the POST data
            $token = $this->post[$this->formTokenLabel];
        } else {
            return false;
        }

        // Grab the stored token
        if ($this->hmac_ip !== false) {
            $expected = $this->hMacWithIp($this->session[$this->sessionTokenLabel]);
        } else {
            $expected = $this->session[$this->sessionTokenLabel];
        }

        return hash_equals($token, $expected);
    }

    public function isValidRequest(): bool
    {
        $isValid = false;
        $currentUrl = $this->getCurrentRequestUrl();
        if (!empty($this->post) && !in_array($currentUrl, $this->excludeUrl, true)) {
            $isValid = $this->validateRequest();
        }
        return $isValid;
    }

    /**
     * removes the token from the session
     */
    public function unsetToken(): void
    {
        if (!empty($this->session[$this->sessionTokenLabel])) {
            unset($this->session[$this->sessionTokenLabel]);
        }
    }
}