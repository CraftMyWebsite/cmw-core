<?php
namespace CMW\Event\Users;

use CMW\Manager\Events\AbstractEvent;

class LoginEvent extends AbstractEvent
{
    public function getName(): string
    {
        return "LoginEvent-Users-CraftMyWebsite"; //avoid simple names :)
    }
}