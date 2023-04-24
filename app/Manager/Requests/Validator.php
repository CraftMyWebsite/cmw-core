<?php

namespace CMW\Manager\Requests;

use CMW\Controller\Core\CoreController;
use CMW\Model\Core\CoreModel;
use JetBrains\PhpStorm\ExpectedValues;

class Validator
{

    /**
     * @var array
     */
    private array $params;

    /**
     * @var string[]
     */
    private array $errors = [];

    /**
     * @param $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public static function Validate(#[ExpectedValues(['string', 'int', 'bool', 'array', 'mixed'])] $type): array
    {
        return [];
    }

    /**
     * @param string ...$keys
     * @return $this
     */
    public function required(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * @param string ...$keys
     * @return $this
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || !empty($value)) {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }

    public function legth(string $key, ?int $min = null, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);

        if (!is_null($min) && !is_null($max) && ($length < $min || $length > $max)){
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }

        if (!is_null($min) && $length < $min){
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }

        if (!is_null($max) && $length > $max){
            $this->addError($key, 'maxLength', [$max]);
            return $this;
        }
    }

    /**
     * @param string $key
     * @param string $rule
     * @param array $attributes
     * @return void
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidationError($key, $rule, $attributes);
    }

    /**
     * @param string $key
     * @return self
     * @desc Check if the element is a slug
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);

        $pattern = '/^([a-z0-9]+-?)+$/';

        if (!is_null($value) && !preg_match($pattern, $this->params[$key])) {
            $this->addError($key, 'slug');
        }

        return $this;
    }

    public function dateTime(string $key): self
    {
        $value = $this->getValue($key);
        $format = (new CoreModel())->fetchOption("dateFormat");

        $date = \DateTime::createFromFormat($format, $value);
        $errors = \DateTime::getLastErrors();


        if ($errors['error_count'] > 0 || $errors['warning_count']) {
            $this->addError($key, 'dateTime', [$format]);
        }
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
    }

}