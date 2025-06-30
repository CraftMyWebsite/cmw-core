<?php
namespace CMW\Event\Users;

use CMW\Manager\Events\AbstractEvent;

class UpdateUserProfileEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'UpdateUserProfileEvent-Users-CraftMyWebsite';  // avoid simple names :)
    }
}
