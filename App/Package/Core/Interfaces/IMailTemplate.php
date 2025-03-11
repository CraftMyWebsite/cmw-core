<?php

namespace CMW\Interface\Core;

/**
 * Interface: @IMailTemplate
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/interfaces
 */
interface IMailTemplate
{
    /**
     * @return string
     * @desc Return the template name
     */
    public function getName(): string;

    /**
     * @return string
     * @desc The variable name cannot have escape or special char
     */
    public function getVarName(): string;

    /**
     * @return string
     * @desc The html CODE need to have [MAIL_CONTENT] !
     */
    public function getCode(): string;

    /**
     * @return string
     * @desc The html CODE
     */
    public function getPreviewImg(): string;
}
