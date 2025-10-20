<?php

namespace CMW\Entity\Users;

use CMW\Manager\Package\AbstractEntity;

/**
 * Class: @UserTokenEntity
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 */
class UserTokenEntity extends AbstractEntity
{
    private int $tokenId;
    private int $userId;
    private string $tokenSelector;
    private string $userAgent;
    private string $ipAddress;
    private string $createdAt;
    private string $lastUsedAt;
    private string $expiresAt;

    /**
     * @param int $tokenId
     * @param int $userId
     * @param string $tokenSelector
     * @param string $userAgent
     * @param string $ipAddress
     * @param string $createdAt
     * @param string $lastUsedAt
     * @param string $expiresAt
     */
    public function __construct(
        int    $tokenId,
        int    $userId,
        string $tokenSelector,
        string $userAgent,
        string $ipAddress,
        string $createdAt,
        string $lastUsedAt,
        string $expiresAt,
    )
    {
        $this->tokenId = $tokenId;
        $this->userId = $userId;
        $this->tokenSelector = $tokenSelector;
        $this->userAgent = $userAgent;
        $this->ipAddress = $ipAddress;
        $this->createdAt = $createdAt;
        $this->lastUsedAt = $lastUsedAt;
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return int
     */
    public function getTokenId(): int
    {
        return $this->tokenId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getTokenSelector(): string
    {
        return $this->tokenSelector;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getLastUsedAt(): string
    {
        return $this->lastUsedAt;
    }

    /**
     * @return string
     */
    public function getExpiresAt(): string
    {
        return $this->expiresAt;
    }

    /**
     * @return string
     * @description Get device icon based on user agent
     */
    public function getDeviceIcon(): string
    {
        if (empty($this->userAgent)) {
            return 'question';
        }

        $userAgent = strtolower($this->userAgent);

        if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android') || str_contains($userAgent, 'iphone')) {
            return 'mobile-screen';
        }

        if (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            return 'tablet-screen-button';
        }

        return 'desktop';
    }

    /**
     * @return string
     * @description Get device name (browser + OS) from user agent
     */
    public function getDeviceName(): string
    {
        if (empty($this->userAgent)) {
            return 'Unknown device';
        }

        $userAgent = strtolower($this->userAgent);

        // Detect browser
        if (str_contains($userAgent, 'firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($userAgent, 'edg')) {
            $browser = 'Edge';
        } elseif (str_contains($userAgent, 'chrome')) {
            $browser = 'Chrome';
        } elseif (str_contains($userAgent, 'safari')) {
            $browser = 'Safari';
        } elseif (str_contains($userAgent, 'opera') || str_contains($userAgent, 'opr')) {
            $browser = 'Opera';
        } else {
            $browser = 'Unknown browser';
        }

        // Detect OS
        if (str_contains($userAgent, 'windows')) {
            $os = 'Windows';
        } elseif (str_contains($userAgent, 'mac')) {
            $os = 'Mac';
        } elseif (str_contains($userAgent, 'linux')) {
            $os = 'Linux';
        } elseif (str_contains($userAgent, 'android')) {
            $os = 'Android';
        } elseif (str_contains($userAgent, 'iphone') || str_contains($userAgent, 'ipad')) {
            $os = 'iOS';
        } else {
            $os = 'Unknown OS';
        }

        return "$browser on $os";
    }

    /**
     * @return bool
     * @description Check if token has expired
     */
    public function isExpired(): bool
    {
        return strtotime($this->expiresAt) < time();
    }
}
