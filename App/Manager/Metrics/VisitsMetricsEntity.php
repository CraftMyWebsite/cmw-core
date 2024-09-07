<?php

namespace CMW\Manager\Metrics;

class VisitsMetricsEntity
{
    private int $id;
    private string $ip;
    private string $date;
    private string $path;
    private ?string $package;
    private int $code;
    private int $isAdmin;

    /**
     * @param int $id
     * @param string $ip
     * @param string $date
     * @param string $path
     * @param string|null $package
     * @param int $code
     * @param int $isAdmin
     */
    public function __construct(int $id, string $ip, string $date, string $path, ?string $package, int $code, int $isAdmin)
    {
        $this->id = $id;
        $this->ip = $ip;
        $this->date = $date;
        $this->path = $path;
        $this->package = $package;
        $this->code = $code;
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getPackage(): ?string
    {
        return $this->package;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getIsAdmin(): int
    {
        return $this->isAdmin;
    }
}
