<?php

namespace CMW\Manager\Xml;


use CMW\Manager\Package\AbstractEntity;
use function base64_encode;

class SitemapItemEntity extends AbstractEntity
{
    private string $loc;
    private string $lastmod;
    private float $priority;
    private string $slug;
    private string $slugEncoded;

    /**
     * @param string $loc
     * @param string $lastmod
     * @param float $priority
     * @param string $slug
     */
    public function __construct(string $loc, string $lastmod, float $priority, string $slug)
    {
        $this->loc = $loc;
        $this->lastmod = $lastmod;
        $this->priority = $priority;
        $this->slug = $slug;

        $this->slugEncoded = base64_encode($this->slug);
    }

    /**
     * @return string
     */
    public function getLoc(): string
    {
        return $this->loc;
    }

    /**
     * @return string
     */
    public function getLastmod(): string
    {
        return $this->lastmod;
    }

    /**
     * @return float
     */
    public function getPriority(): float
    {
        return $this->priority;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * <p>Base64 encoded slug</p>
     * @return string
     */
    public function getSlugEncoded(): string
    {
        return $this->slugEncoded;
    }
}