<?php
namespace CMW\Event\Users;

use CMW\Manager\Events\AbstractEvent;

class RegisterEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'RegisterEvent-Users-CraftMyWebsite';  // avoid simple names :)
    }
}
