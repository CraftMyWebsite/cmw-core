<?php

namespace CMW\Entity\Core;

class MenuEntity
{
    private int $id;
    private string $name;
    private string $url;
    private int $isRestricted;
    private int $order;
    private int $targetBlank;

    /**
     * @param int $id
     * @param string $name
     * @param string $url
     * @param int $isRestricted
     * @param int $order
     * @param int $targetBlank
     */
    public function __construct(int $id, string $name, string $url, int $isRestricted, int $order, int $targetBlank)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->isRestricted = $isRestricted;
        $this->order = $order;
        $this->targetBlank = $targetBlank;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function isRestricted(): bool
    {
        return $this->isRestricted;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isTargetBlank(): bool
    {
        return $this->targetBlank;
    }
}