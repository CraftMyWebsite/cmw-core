<?php

namespace CMW\Manager\Events;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Listener
{
    public function __construct(
        private readonly string $eventName,
        private readonly int $times = 0,
        private readonly int $weight = 1
    ) {}

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @return int
     */
    public function getTimes(): int
    {
        return $this->times;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }
}
