<?php

namespace CMW\Manager\Components;

use CMW\Manager\Components\Base\ButtonComponentBase;
use CMW\Manager\Components\Base\InputComponentBase;

abstract class IComponent
{

    protected ?string $id = null;
    protected string $classes = "";

    /**
     * @param string|null $id
     * @return InputComponentBase
     */
    public function setId(?string $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    protected function showId(): string
    {
        return $this->id ? "id='$this->id'" : '';
    }

    /**
     * @param string $classes
     * @return $this
     */
    public function setClasses(string $classes): static
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * @return static
     * @desc Create a new instance of the component.
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @return void
     * @desc Render the component.
     */
    abstract public function render(): void;
}