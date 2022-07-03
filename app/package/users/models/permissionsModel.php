<?php

namespace CMW\Model\Permissions;

use CMW\Entity\Roles\roleEntity;
use CMW\Entity\Users\userEntity;
use CMW\Entity\Permissions\permissionEntity;
use CMW\Model\manager;

/**
 * Class: @permissionsModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class permissionsModel extends manager
{

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        $toReturn = array();

        /* Get all parents + desc */
        $sql = "SELECT permParent.*, permDesc.permission_desc_code_parent, permDesc.permission_desc_value, permission_desc_lang 
                FROM cmw_permissions_parent AS permParent 
                JOIN cmw_permissions_desc permDesc
                ON permParent.permission_parent_code = permDesc.permission_desc_code_parent
                WHERE permDesc.permission_desc_lang = :lang";
        $db = manager::dbConnect();

        $resParent = $db->prepare($sql);

        if($resParent->execute(array("lang" => getenv("LOCALE")))){

            $resParent = $resParent->fetchAll();


            foreach ($resParent as $parent){


                $sql = "SELECT permChild.*, permDesc.permission_desc_code_parent, permDesc.permission_desc_value, permission_desc_lang
                FROM cmw_permissions_child AS permChild
                JOIN cmw_permissions_desc permDesc
                ON permChild.permission_child_code = permDesc.permission_desc_code_child
                WHERE permChild.permission_child_parent = :parent";

                $resChild = $db->prepare($sql);



                if(!$resChild->execute(array("parent" => $parent['permission_parent_code']))){
                    continue;
                }

                $resChild = $resChild->fetchAll();


                if(!$resChild){
                    continue;
                }


                $toReturn += array($parent['permission_parent_package'] => [
                    "package" => $parent['permission_parent_package'],
                    "parent_code" => $parent['permission_parent_code'],
                    "parent_editable" => $parent['permission_parent_editable'],
                    "parent_desc_value" => $parent['permission_desc_value'],
                    "perms_childs" => []]);

                foreach ($resChild as $child){
                    $toReturn[$parent['permission_parent_package']]['perms_childs'] += [
                            $child['permission_child_code'] => [
                            "child_code" => $child['permission_child_code'],
                            "child_editable" => $child['permission_child_editable'],
                            "child_desc_value" => $child['permission_desc_value']
                        ]
                  ];
                }



            }
        }






        return $toReturn;
    }
}