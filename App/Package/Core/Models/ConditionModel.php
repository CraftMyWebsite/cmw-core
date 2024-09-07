<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\ConditionEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;

class ConditionModel extends AbstractModel
{
    /**
     * @return \CMW\Entity\Core\ConditionEntity|null
     */
    public function getCGU(): ?ConditionEntity
    {
        $sql = 'SELECT * FROM cmw_core_condition WHERE condition_id = 2';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return null;
        }

        $res = $res->fetch();

        $author = (new UsersModel())->getUserById($res['condition_last_editor']);

        return new ConditionEntity(
            $res['condition_id'],
            $res['condition_content'],
            $res['condition_state'],
            $res['condition_updated'],
            $author
        );
    }

    /**
     * @param int $conditionId
     * @param string|null $conditionContent
     * @param int $conditionState
     * @param int $conditionAuthor
     * @return \CMW\Entity\Core\ConditionEntity|null
     */
    public function updateCondition(?string $cguContent, int $cguState, ?string $cgvContent, int $cgvState, int $author): ?ConditionEntity
    {
        $info = array(
            'cguContent' => $cguContent,
            'cguState' => $cguState,
            'cgvContent' => $cgvContent,
            'cgvState' => $cgvState,
            'author' => $author,
        );

        $sql = 'UPDATE cmw_core_condition
SET
    condition_content = CASE
        WHEN condition_id = 1 THEN :cgvContent
        WHEN condition_id = 2 THEN :cguContent
        ELSE condition_content
    END,
    condition_state = CASE
        WHEN condition_id = 1 THEN :cgvState
        WHEN condition_id = 2 THEN :cguState
        ELSE condition_state
    END,
    condition_last_editor = :author
WHERE condition_id IN (1, 2);';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($info)) {
            return $this->getCGV();
        }

        return null;
    }

    /**
     * @return \CMW\Entity\Core\ConditionEntity|null
     */
    public function getCGV(): ?ConditionEntity
    {
        $sql = 'SELECT * FROM cmw_core_condition WHERE condition_id = 1';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return null;
        }

        $res = $res->fetch();

        $author = (new UsersModel())->getUserById($res['condition_last_editor']);

        return new ConditionEntity(
            $res['condition_id'],
            $res['condition_content'],
            $res['condition_state'],
            $res['condition_updated'],
            $author
        );
    }
}
