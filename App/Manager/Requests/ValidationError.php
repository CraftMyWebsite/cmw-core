<?php

namespace CMW\Manager\Requests;

use CMW\Manager\Lang\LangManager;

class ValidationError
{
    private string $key;
    private string $rule;
    private array $attributes;


    /**
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(string $key, string $rule, array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }

    public function __toString(): string
    {
        return match ($this->rule) {
            'required' => LangManager::translate("core.errors.requests.required", ['key' => $this->key]),
            'empty' => LangManager::translate("core.errors.requests.empty", ['key' => $this->key]),
            'slug' => LangManager::translate("core.errors.requests.slug", ['key' => $this->key]),
            'minLength' => LangManager::translate("core.errors.requests.minLength",
                ['key' => $this->key, 'min' => $this->attributes['min']]),
            'maxLength' => LangManager::translate("core.errors.requests.maxLength",
                ['key' => $this->key, 'max' => $this->attributes['max']]),
            'betweenLength' => LangManager::translate("core.errors.requests.betweenLength",
                ['key' => $this->key, 'min' => $this->attributes['min'], 'max' => $this->attributes['max']]),
            'dateTime' => LangManager::translate("core.errors.requests.dateTime",
                ['key' => $this->key, 'format' => $this->attributes['format']]),
            'getValue' => LangManager::translate("core.errors.requests.getValue", ['key' => $this->key]),
            'type' => LangManager::translate("core.errors.requests.type", ['key' => $this->key])
        };
    }


}
