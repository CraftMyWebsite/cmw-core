<?php
namespace CMW\Event\Users;

use CMW\Manager\Events\AbstractEvent;

class LogoutEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'LogoutEvent-Users-CraftMyWebsite';  // avoid simple names :)
    }
}
