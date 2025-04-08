<?php

namespace CMW\Manager\Theme\Editor;

use CMW\Manager\Package\AbstractEntity;

class EditorRangeOptions extends AbstractEntity
{
    public float $min;
    public float $max;
    public float $step;
    public string $prefix;
    public string $suffix;

    /**
     * @param float $min
     * @param float $max
     * @param float $step
     * @param string $prefix
     * @param string $suffix
     */
    public function __construct(float $min, float $max, float $step, string $prefix = "", string $suffix = "")
    {
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }

    /**
     * @return float
     */
    public function getMin(): float
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getMax(): float
    {
        return $this->max;
    }

    /**
     * @return float
     */
    public function getStep(): float
    {
        return $this->step;
    }

    /**
     * @return string|null
     */
    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    /**
     * @return string|null
     */
    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

}
