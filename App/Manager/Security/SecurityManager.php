<?php

namespace CMW\Manager\Security;

use Error;
use Exception;

class SecurityManager extends HoneyInput
{
    private string $formTokenLabel = 'security-csrf-token';
    private string $formTokenIdLabel = 'security-csrf-token-id';
    private string $sessionTokenPrefix = 'CSRF_TOKEN_SESS_ID_';
    private array $post = [];
    private array $session = [];
    private array $server = [];
    private mixed $excludeUrl = [];
    private string $hashAlgo = 'sha256';
    private bool $hmac_ip = true;
    private string $hmacData = 'ABCeNBHVe3kmAqvU2s7yyuJSF2gpxKLC';

    public function __construct($excludeUrl = null, &$post = null, &$session = null, &$server = null)
    {
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
        $csrfTokenId = bin2hex(random_bytes(8));
        $csrfToken = $this->getCSRFToken($csrfTokenId);

        echo '<input type="hidden" name="' . $this->xssafe($this->formTokenLabel) . '" value="' . $this->xssafe($csrfToken) . '" />';
        echo '<input type="hidden" name="' . $this->xssafe($this->formTokenIdLabel) . '" value="' . $this->xssafe($csrfTokenId) . '" />';

        $this->generateHoneyInput();
    }

    public function getCSRFToken(string $tokenId): string
    {
        $sessionTokenLabel = $this->sessionTokenPrefix . $tokenId;

        if (empty($this->session[$sessionTokenLabel])) {
            try {
                $this->session[$sessionTokenLabel] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                $this->session[$sessionTokenLabel] = bin2hex(microtime() + $e);
            }
        }

        if ($this->hmac_ip !== false) {
            $token = $this->hMacWithIp($this->session[$sessionTokenLabel]);
        } else {
            $token = $this->session[$sessionTokenLabel];
        }

        return $token;
    }

    private function hMacWithIp($token): string
    {
        return hash_hmac($this->hashAlgo, $this->hmacData, $token);
    }

    private function xssafe($data): string
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, 'UTF-8');
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
        $protocol = 'http';
        if (isset($this->server['HTTPS'])) {
            $protocol = 'https';
        }
        return $protocol . '://' . $this->server['HTTP_HOST'] . $this->server['REQUEST_URI'];
    }

    public function validateRequest(): bool
    {
        if (!isset($this->post[$this->formTokenIdLabel])) {
            return false;
        }

        $tokenId = $this->post[$this->formTokenIdLabel];
        $sessionTokenLabel = $this->sessionTokenPrefix . $tokenId;

        if (!isset($this->session[$sessionTokenLabel])) {
            return false;
        }

        if (!empty($this->post[$this->formTokenLabel])) {
            $token = $this->post[$this->formTokenLabel];
        } else {
            return false;
        }

        if ($this->hmac_ip !== false) {
            $expected = $this->hMacWithIp($this->session[$sessionTokenLabel]);
        } else {
            $expected = $this->session[$sessionTokenLabel];
        }

        return hash_equals($token, $expected);
    }

    /**
     * removes the token from the session
     */
    public function unsetToken(): void
    {
        if (isset($this->post[$this->formTokenIdLabel])) {
            $tokenId = $this->post[$this->formTokenIdLabel];
            $sessionTokenLabel = $this->sessionTokenPrefix . $tokenId;
            unset($this->session[$sessionTokenLabel]);
        }
    }
}
