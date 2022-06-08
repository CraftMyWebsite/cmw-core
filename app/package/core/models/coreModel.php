<?php

namespace CMW\Model;

/**
 * Class: @coreController
 * @package Core
 * @author LoGuardiaN & Teyir | <loguardian@hotmail.com>
 * @version 1.0
 */
class coreModel extends manager
{
    public string $theme;
    public array $menu;
    public static string $minecraft_ip;
    public static string $name;
    public static string $description;

    public function fetchOption($option): void
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');
        $req->execute(array($option));
        $option = $req->fetch();

        $this->theme = $option['option_value'];
    }

    /***
     * @param $option
     * @return string
     * @desc get the selected option
     */
    public static function getOptionValue($option): string
    {
        $db = self::dbConnect();
        $req = $db->prepare('SELECT option_value FROM cmw_core_options WHERE option_name = ?');
        $req->execute(array($option));
        if($req->execute()) {
            return $req->fetch()["option_value"];
        }

        return "";
    }

    public function fetchOptions(): array
    {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT * FROM cmw_core_options');

        if($req->execute()) {
            return $req->fetchAll();
        }
        return [];
    }

    public static function updateOption(string $option_name, string $option_value): void
    {
        $db = self::dbConnect();
        $req = $db->prepare('UPDATE cmw_core_options SET option_value=:option_value, option_updated=now() WHERE option_name=:option_name');
        $req->execute(array("option_name" => $option_name, "option_value" => $option_value));
    }

    public static function getLanguages(string $prefix): array|string
    {
        foreach (get_defined_constants(false) as $key=>$value)
            if (str_starts_with($key, mb_strtoupper($prefix, 'UTF-8')))  $dump[$key] = $value;


        return !empty($dump) ? $dump : "Error: No Constants found with prefix '".$prefix."'";
    }
}