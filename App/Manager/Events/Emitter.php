<?php

namespace CMW\Manager\Events;

use CMW\Manager\Collections\Collection;
use CMW\Manager\Collections\CollectionEntity;
use CMW\Manager\Loader\Loader;
use CMW\Utils\Log;
use JetBrains\PhpStorm\ExpectedValues;
use Closure;
use ReflectionClass;
use ReflectionMethod;

class Emitter
{
    private static array $listenerCounter = array();
    private static AbstractEvent $actualEvent;

    private static function loadAttributeByEvent(array $attributeList, #[ExpectedValues(AbstractEvent::class)] string $eventName, array &$eventAttributes): void
    {
        /**
         * @var $attr ReflectionClass
         * @var $method ReflectionMethod
         */
        foreach ($attributeList as [$attr, $method]) {
            /** @var Listener $attributeInstance */
            $attributeInstance = $attr->newInstance();

            // todo use GlobalObject getInstance
            if ($eventName !== $attributeInstance->getEventName()) {
                continue;
            }

            if (!isset(self::$listenerCounter[$eventName][$method->getName()])) {
                self::$listenerCounter[$eventName][$method->getName()] = 0;
            }

            $eventAttributes[] = [$attributeInstance, $method];
        }
    }

    private static function sortAttributes(array &$eventAttributes): void
    {
        usort($eventAttributes, static function (array $a, array $b) {
            [
                $firstAttr,
            ] = $a;
            [
                $secondAttr,
            ] = $b;
            return $secondAttr->getWeight() - $firstAttr->getWeight();
        });
    }

    /**
     * @throws \ReflectionException
     */
    private static function initEventClass(#[ExpectedValues(AbstractEvent::class)] string $eventName): void
    {
        self::$actualEvent = (new ReflectionClass($eventName))->getMethod('getInstance')->invoke(null);
        self::$actualEvent->init();
    }

    private static function hasExceededCall(Listener $listener, int $listenerCounter): bool
    {
        return $listener->getTimes() !== 0 && $listenerCounter > 0 && $listenerCounter >= $listener->getTimes();
    }

    private static function getCounterByMethod(#[ExpectedValues(AbstractEvent::class)] string $eventName, ReflectionMethod $method): int
    {
        return self::$listenerCounter[$eventName][$method->getName()];
    }

    /**
     * @throws \ReflectionException
     */
    private static function invokeEventMethod(#[ExpectedValues(AbstractEvent::class)] string $eventName, ReflectionMethod $method, mixed $data): void
    {
        $controller = $method->getDeclaringClass()->getMethod('getInstance')->invoke(null);
        $method->invoke($controller, $data);

        self::increment($eventName, $method);
    }

    private static function increment(#[ExpectedValues(AbstractEvent::class)] string $eventName, ReflectionMethod $method): void
    {
        self::$listenerCounter[$eventName][$method->getName()]++;
    }

    /**
     * @throws \ReflectionException
     */
    private static function invoke(#[ExpectedValues(AbstractEvent::class)] string $eventName, array $eventAttributes, mixed $data): void
    {
        /* @var \CMW\Manager\Events\Listener $attr */
        /* @var ReflectionMethod $method */
        foreach ($eventAttributes as [$attr, $method]) {
            if (!self::$actualEvent->canPropagate()) {
                break;
            }

            if (self::hasExceededCall($attr, self::getCounterByMethod($eventName, $method))) {
                continue;
            }

            self::invokeEventMethod($eventName, $method, $data);
        }
    }

    /**
     * @throws \ReflectionException
     */
    public static function send(#[ExpectedValues(AbstractEvent::class)] string $eventName, mixed $data): void
    {
        $attributeList = Loader::getAttributeList()[Listener::class];
        $eventAttributes = array();

        if (empty($attributeList)) {
            return;
        }

        if (!isset(self::$listenerCounter[$eventName])) {
            self::$listenerCounter[$eventName] = array();
        }

        self::loadAttributeByEvent($attributeList, $eventName, $eventAttributes);

        if (empty($eventAttributes)) {
            return;
        }

        self::sortAttributes($eventAttributes);

        self::initEventClass($eventName);

        self::invoke($eventName, $eventAttributes, $data);
    }
}
