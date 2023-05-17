<?php

namespace CMW\Manager\Events;

use Closure;
use CMW\Manager\Collections\Collection;
use CMW\Manager\Collections\CollectionEntity;
use CMW\Manager\Loader\Loader;
use CMW\Utils\Log;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionClass;
use ReflectionMethod;

class Emitter
{
    private static array $listenerCounter = array();

    /**
     * @throws \ReflectionException
     */
    public static function send(#[ExpectedValues(AbstractEvent::class)] string $eventName, mixed $data): void
    {

        $attributeList = Loader::getAttributeList()[Listener::class];

        if (empty($attributeList)) {
            return;
        }

        $eventAttributes = array();

        if (!isset(static::$listenerCounter[$eventName])) {
            static::$listenerCounter[$eventName] = array();
        }

        /**
         * @var $attr ReflectionClass
         * @var $method ReflectionMethod
         */
        foreach ($attributeList as [$attr, $method]) {

            /** @var Listener $attributeInstance */
            $attributeInstance = $attr->newInstance();

            //todo use GlobalObject getInstance
            if ($eventName !== $attributeInstance->getEventName()) {
                continue;
            }

            if (!isset(static::$listenerCounter[$eventName][$method->getName()])) {
                static::$listenerCounter[$eventName][$method->getName()] = 0;
            }

            $eventAttributes[] = [$attributeInstance, $method];
        }

        if (empty($eventAttributes)) {
            return;
        }

        usort($eventAttributes, static function (array $a, array $b) {
            [$firstAttr, ] = $a;
            [$secondAttr, ] = $b;
            return $secondAttr->getWeight() - $firstAttr->getWeight();
        });


        /* @var \CMW\Manager\Events\AbstractEvent $eventClass */
        $eventClass = (new ReflectionClass($eventName))->getMethod("getInstance")->invoke(null);
        $eventClass->init();

        /**
         * @var \CMW\Manager\Events\Listener $attr
         * @var ReflectionMethod $method
         */
        foreach ($eventAttributes as [$attr, $method]) {

            if (!$eventClass->canPropagate()) {
                break;
            }

            if($attr->getTimes() !== 0 &&static::$listenerCounter[$eventName][$method->getName()] > 0 && static::$listenerCounter[$eventName][$method->getName()] >= $attr->getTimes()) {
                continue;
            }

            $controller = $method->getDeclaringClass()->getMethod("getInstance")->invoke(null);
            $method->invoke($controller, $data);

            static::$listenerCounter[$eventName][$method->getName()]++;
        }

        //Log::debug(static::$listenerCounter);
    }

}