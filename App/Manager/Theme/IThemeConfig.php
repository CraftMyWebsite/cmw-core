<?php

namespace CMW\Manager\Theme;

/**
 * @desc Use this interface only for theme configuration
 * @see <b>SOON WIKI LINK</b>
 */
interface IThemeConfig
{
    /**
     * @return string
     * @desc The theme name. (Same as Folder Theme Name)
     */
    public function name(): string;

    /**
     * @return string
     * @desc The theme version. Never use the precedents known by the market
     */
    public function version(): string;

    /**
     * @return string
     * @desc The compatible CMW (CORE) version (always specify the latest one when updating)
     */
    public function cmwVersion(): string;

    /**
     * @return string|null
     * @desc Return the image link of the theme (optional). Return null if not exist.
     */
    public function imageLink(): ?string;

    /**
     * @return string|null
     * @desc The theme author.
     */
    public function author(): ?string;

    /**
     * @return string[]
     * @desc <p>Ex: ['Teyir', 'Zomb']</p>.
     */
    public function authors(): array;

    /**
     * @return string[]
     * @desc List all the compatibles packages
     */
    public function compatiblesPackages(): array;

    /**
     * @return string[]
     * @desc List all the required packages. For automatic installation
     */
    public function requiredPackages(): array;
}
