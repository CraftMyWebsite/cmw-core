<?php

namespace CMW\Manager\ORM\Database;

use CMW\Manager\ORM\SGBD\Data\SGBDReceiver;

interface SGBD
{
    public function connect(): mixed;
    public function generate(SGBDReceiver $receiver): array;
}
