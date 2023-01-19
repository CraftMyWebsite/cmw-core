<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\ConditionEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Utils\Utils;

use CMW\Model\Users\UsersModel;

class ConditionModel extends DatabaseManager
{
	public function getCGV(): ?ConditionEntity
    {
        $sql = "SELECT * FROM cmw_condition WHERE condition_id = 1";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute()) {
            return null;
        }

        $res = $res->fetch();

        $author = (new UsersModel())->getUserById($res["condition_author"]);

        return new ConditionEntity(
            $res['condition_id'],
            $res['condition_content'],
            $res['condition_state'],
            $res['condition_updated'],
            $author
        );
    }
    public function getCGU(): ?ConditionEntity
    {
        $sql = "SELECT * FROM cmw_condition WHERE condition_id = 2";

        $db = self::getInstance();
        $res = $db->prepare($sql);


        if (!$res->execute()) {
            return null;
        }

        $res = $res->fetch();

        $author = (new UsersModel())->getUserById($res["condition_author"]);

        return new ConditionEntity(
            $res['condition_id'],
            $res['condition_content'],
            $res['condition_state'],
            $res['condition_updated'],
            $author
        );
    }
    public function updateCondition(int $conditionId, ?string $conditionContent, int $conditionState, int $conditionAuthor): ?ConditionEntity
    {
        $info = array(
        	"conditionId" => $conditionId,
            "conditionContent" => $conditionContent,
            "conditionState" => $conditionState,
            "conditionAuthor" => $conditionAuthor,
        );

        $sql = "UPDATE cmw_condition SET condition_content = :conditionContent, condition_state = :conditionState, condition_author = :conditionAuthor WHERE condition_id = :conditionId";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($info)) {
            return $this->getCGV();
        }

        return null;
    }
}