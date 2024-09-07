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
     * @desc The theme name.
     */
    public function name(): string;

    /**
     * @return string
     * @desc The theme version. Please use the same as the CMW Market version.
     */
    public function version(): string;

    /**
     * @return string
     * @desc The current supported CMW Version.
     */
    public function cmwVersion(): string;

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
     * @desc List all the compatibles packages <b>list optional packages.</b>
     */
    public function compatiblesPackages(): array;

    /**
     * @return string[]
     * @desc List all the required packages.
     */
    public function requiredPackages(): array;
}
