<?php

namespace CMW\Manager\Events;

use Closure;
use CMW\Manager\Collections\Collection;
use CMW\Manager\Collections\CollectionEntity;
use CMW\Utils\Loader;
use JetBrains\PhpStorm\ExpectedValues;
use ReflectionClass;
use ReflectionMethod;

class Emitter
{

    private static function getData(): CollectionEntity {
        return Collection::getInstance()->get("emitter");
    }

    private static function getEventData(string $eventName): CollectionEntity | null {
        return Collection::getInstance()->get("emitter")->getWithKey($eventName)?->getWithKey($eventName);
    }


    /**
     * @throws \ReflectionException
     */
    public static function listen(#[ExpectedValues(AbstractEvent::class)] string $eventName, Closure $closure): void
    {

        $event = new ReflectionClass($eventName);
        /* @var \CMW\Manager\Events\AbstractEvent $eventInstance*/
        $eventInstance = $event->newInstance();

        $eventInstance->init();

        echo $eventInstance->getName() . "<br>";
        $eventInstance->increment();
        echo $eventInstance->getCounter();
    }

    public static function send(#[ExpectedValues(AbstractEvent::class)] string $eventName, mixed $data): void
    {

        $attrubuteList = Loader::getAttributeList()[Listener::class];

        $eventAttributes = array();

        if(!isset($attrubuteList)) {
            return;
        }

        foreach($attrubuteList as [$attr, $method]) {

            if($eventName !== $attr->newInstance()->getEventName()) {
                continue;
            }

            $eventAttributes[] = [$attr->newInstance(), $method];
        }

        usort($eventAttributes, static function (array $a, array $b) {
            return $a[0]->getWeight() > $b[0]->getWeight();
        });


        /**
         * @var \CMW\Manager\Events\AbstractEvent $eventClass
         */
        $eventClass = (new \ReflectionClass($eventName))->newInstance();
        $eventClass->init();

        /**
         * @var \CMW\Manager\Events\Listener $attr
         * @var ReflectionMethod $method
         */
        foreach($eventAttributes as [$attr, $method]) {

            /**
             * Ici, le counter est mal fait, car on compte le nombre de fois que la classe à été appelée, alors qu'on veut sur la méthode, alors il faut le faire du'une autre façon ? :)
             */
            var_dump($eventClass->getCounter(), $attr->getTimes(), $eventClass->canPropagate());

            //if($eventClass->getCounter() < $attr->getTimes()) {
            if(false) {
                continue;
            }

            if(!$eventClass->canPropagate()) {
                break;
            }

            $controller = $method->getDeclaringClass()->newInstance();
            $methodName = $method->getName();
            $controller->$methodName($data);


            $eventClass->increment();

        }
    }

}