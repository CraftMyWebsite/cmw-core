<?php

namespace CMW\Interface\Core;

interface ICaptcha
{
    /**
     * @return string
     * @desc Get the captcha name, ex: reCAPTCHA v2
     */
    public function getName(): string;

    /**
     * @return string
     * @desc Get the captcha code name, ex: recaptcha-v2
     */
    public function getCode(): string;

    /**
     * @return void
     * @desc Print the captcha
     */
    public function show(): void;

    /**
     * @return bool
     * @desc Validate the captcha
     */
    public function validate(): bool;

    public function adminForm();

    /**
     * @return void
     * @desc Call when the keys are edited
     */
    public function adminFormPost(): void;
}
