<?php

namespace CMW\Manager\Events;

use CMW\Manager\Collections\Collection;
use CMW\Manager\Collections\CollectionEntity;

abstract class AbstractEvent
{
    private static CollectionEntity $counterCollection;

    private static bool $continuePropagation;
    private static int $counterCall;


    public function init(): void
    {
        static::$counterCollection = Collection::getInstance()->get("eventListener");
        if (is_null(static::$counterCollection->getWithKey(static::class))) {
            static::$counterCollection->addWithKey(static::class, [true, 1]);
        }

        [static::$continuePropagation, static::$counterCall] = self::$counterCollection->getWithKey(static::class);
    }


    abstract public function getName(): string;

    public function getCounter(): int
    {
        return static::$counterCall;
    }

    public function canPropagate(): bool
    {
        return static::$continuePropagation;
    }

    public function increment(): void
    {
        static::$counterCollection->addWithKey(static::class, [$this->canPropagate(), $this->getCounter() + 1]);
    }

    public function stopPropagation(): void
    {
        static::$counterCollection->addWithKey(static::class, [false, $this->getCounter() + 1]);
    }


}