<?php

namespace CMW\Model\Pages;

use CMW\Entity\Pages\PageEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;
use JsonException;
use PDOStatement;

/**
 * Class: @PagesModel
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PagesModel extends AbstractModel
{

    /*=> GET */

    public function getPageById(int $id): ?PageEntity
    {
        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, page_created, page_updated 
                FROM cmw_pages WHERE page_id = :page_id";

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("page_id" => $id))) {
            return null;
        }

        return $this->fetchPageResult($res);
    }

    public function getPageBySlug(string $slug): ?PageEntity
    {
        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, 
                page_created, page_updated FROM cmw_pages WHERE page_slug = :page_slug";

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(array("page_slug" => $slug))) {
            return null;
        }

        return $this->fetchPageResult($res);
    }

    /**
     * @return \CMW\Entity\Pages\PageEntity[]
     */
    public function getPages(): array
    {

        $sql = "select page_id from cmw_pages";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($page = $res->fetch()) {
            $toReturn[] = $this->getPageById($page["page_id"]);
        }

        return $toReturn;

    }

    /*=> CREATE */

    public function createPage(string $title, string $slug, string $content, int $userId, int $state): ?PageEntity
    {
        $data = array(
            "page_title" => mb_strimwidth($title, 0, 255),
            "page_slug" => $slug,
            "page_content" => $content,
            "user_id" => $userId,
            "page_state" => $state
        );

        $sql = "INSERT INTO cmw_pages(page_title, page_slug, page_content, user_id, page_state) 
                VALUES (:page_title, :page_slug, :page_content, :user_id, :page_state)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getPageById($id);
        }

        return null;
    }

    /*=> DELETE */

    public function deletePage(int $id): bool
    {
        $var = array(
            "page_id" => $id,
        );
        $sql = "DELETE FROM cmw_pages WHERE page_id=:page_id";

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute($var);
    }

    /*=> UPDATE */

    public function updatePage(int $id, string $slug, string $title, string $content, int $state): ?PageEntity
    {
        $var = array(
            "page_id" => $id,
            "page_slug" => $slug,
            "page_title" => mb_strimwidth($title, 0, 255),
            "page_content" => $content,
            "page_state" => $state
        );

        $sql = "UPDATE cmw_pages SET page_slug=:page_slug, page_title=:page_title,
                     page_content=:page_content, page_state=:page_state WHERE page_id=:page_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($var)) {
            $this->updateEditTime($id);
            return $this->getPageById($id);
        }

        return null;
    }

    public function updateEditTime(int $id): void
    {
        $var = array(
            "page_id" => $id,
        );

        $sql = "UPDATE cmw_pages SET page_updated = NOW() WHERE page_id=:page_id";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    private function fetchPageResult(PDOStatement $res): ?PageEntity
    {
        $res = $res->fetch();
        $user = (new UsersModel())->getUserById($res["user_id"]);

        if(!$user) {
            return null;
        }

        return new PageEntity(
            $res["page_id"],
            $res["page_slug"],
            $res["page_title"],
            $res["page_content"],
            $user,
            $res["page_content"] ?? "",
            $res["page_state"],
            $res["page_created"],
            $res["page_updated"],
        );

    }
}
