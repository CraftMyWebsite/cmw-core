<?php
namespace CMW\Event\Users;

use CMW\Manager\Events\AbstractEvent;

class DeleteUserAccountEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'DeleteUserAccountEvent-Users-CraftMyWebsite';  // avoid simple names :)
    }
}
