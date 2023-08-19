<?php

namespace CMW\Manager\ORM;

use CMW\Manager\ORM\Database\DatabaseManager;
use CMW\Manager\ORM\Database\SGBD;
use CMW\Manager\ORM\SGBD\Actions;
use CMW\Manager\ORM\SGBD\Data\SGBDReceiver;
use CMW\Manager\Package\AbstractEntity;
use CMW\Utils\Log;

class ORM
{
    //TODO : Store actions, clauses and do execute method



    private function __construct(
        private readonly ?SGBD $_sgbdInstance,
        private readonly SGBDReceiver $_sgbdReceiver
    )
    {
    }

    public function getSGBD(): SGBD
    {
        return $this->_sgbdInstance;
    }

    public function getReceiver(): SGBDReceiver
    {
        return $this->_sgbdReceiver;
    }

    public function execute(): array
    {
        return $this->getSGBD()->generate($this->getReceiver());
    }

    public static function getInstance(?SGBD $sgbdInstance = null): Actions
    {
        $sgbd = $sgbdInstance ?? DatabaseManager::getInstance();

        if ($sgbd === null) {
            throw new \Exception("No SGBD instance found");
        }

        return new Actions(new self($sgbd, new SGBDReceiver()));
    }

}