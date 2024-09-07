<?php

namespace CMW\Manager\Xml;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Utils\File;

class SitemapManager extends AbstractManager
{
    /**
     * @return bool
     * @desc Generate XML file with default location
     */
    public function init(): bool
    {
        $url = EnvManager::getInstance()->getValue('PATH_URL');
        $date = date('c');

        $data = <<<XML
            <?xml version="1.0" encoding="UTF-8"?>
            <urlset 
                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 
                    http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
                <url>
                    <loc>$url</loc>
                    <lastmod>$date</lastmod>
                    <priority>1.00</priority>
                </url>
            </urlset>
            XML;

        $file = EnvManager::getInstance()->getValue('DIR') . 'sitemap.xml';

        return File::write(
            $file,
            $data,
        );
    }

    /**
     * @param string $slug ex: news/get-started
     * @param float $priority ex: 0.90
     * @return bool
     */
    public function add(string $slug, float $priority): bool
    {
        $file = EnvManager::getInstance()->getValue('DIR') . 'sitemap.xml';

        if (empty(File::read($file))) {
            $this->init();
        }

        $content = XmlManager::getInstance()->read($file);

        if (!$content) {
            return false;
        }

        $date = date('c');
        $loc = EnvManager::getInstance()->getValue('PATH_URL') . $slug;

        $object = $content->addChild('url');
        $object?->addChild('loc', $loc);
        $object?->addChild('lastmod', $date);
        $object?->addChild('priority', $priority);

        $formattedData = $content->asXML();

        if (!$formattedData) {
            return false;
        }

        return File::write($file, $formattedData);
    }

    /**
     * @param string $slug ex: news/get-started
     * @param float $priority
     * @return bool
     * @desc
     * <p>
     *     Update <b>Priority and date</b> for an url.
     * </p>
     */
    public function update(string $slug, float $priority): bool
    {
        $file = EnvManager::getInstance()->getValue('DIR') . 'sitemap.xml';

        $content = XmlManager::getInstance()->read($file);

        if (!$content) {
            return false;
        }

        $loc = EnvManager::getInstance()->getValue('PATH_URL') . $slug;

        foreach ($content->url as $url) {
            if ((string) $url->loc === $loc) {
                $url->lastmod = date('c');
                $url->priority = $priority;

                $formattedData = $content->asXML();

                if (!$formattedData) {
                    return false;
                }

                return File::write($file, $formattedData);
            }
        }

        return false;
    }

    /**
     * @param string $slug ex: news/get-started
     * @return bool
     * @desc
     * <p>
     *     Delete url object.
     * </p>
     */
    public function delete(string $slug): bool
    {
        $file = EnvManager::getInstance()->getValue('DIR') . 'sitemap.xml';

        $content = XmlManager::getInstance()->read($file);

        if (!$content) {
            return false;
        }

        $loc = EnvManager::getInstance()->getValue('PATH_URL') . $slug;

        for ($i = 0, $iMax = count($content->url); $i < $iMax; $i++) {
            $url = $content->url[$i];

            if ((string) $url->loc === $loc) {
                unset($content->url[$i]);

                $formattedData = $content->asXML();

                if (!$formattedData) {
                    return false;
                }

                return File::write($file, $formattedData);
            }
        }
        return false;
    }
}
