<?php

namespace CMW\Manager\ORM;

use CMW\Manager\ORM\Database\DatabaseManager;
use CMW\Manager\ORM\Database\SGBD;
use CMW\Manager\ORM\SGBD\Actions;
use CMW\Manager\Package\AbstractEntity;

class ORM
{
    //TODO : Store actions, clauses and do execute method

    private function __construct(
        private readonly SGBD $_sgbdInstance
    )
    {
    }

    public function getSGBD(): SGBD
    {
        return $this->_sgbdInstance;
    }

    public function execute(): array
    {
        echo "execute";
        return array();
    }

    public static function getInstance(?SGBD $sgbdInstance): Actions
    {
        $sgbd = $sgbdInstance ?? DatabaseManager::getInstance();
        return new Actions(new self($sgbd));
    }

}