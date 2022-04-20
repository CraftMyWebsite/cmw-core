<?php

namespace CMW\Model;

/**
 * Class: @coreController
 * @package Core
 * @author LoGuardiaN | <loguardian@hotmail.com>
 * @version 1.0
 */
class coreModel extends manager
{
    public string $theme;
    public array $menu;

    public function fetchOption($option): void
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');
        $req->execute(array($option));
        $option = $req->fetch();

        $this->theme = $option['option_value'];
    }

    public function updateOption($option_infos): void
    {
        $db = $this->dbConnect();
        $req = $db->prepare('UPDATE cmw_core_options SET option_value=:option_value, option_updated=now() WHERE option_name=:option_name');
        $req->execute($option_infos);
    }
}