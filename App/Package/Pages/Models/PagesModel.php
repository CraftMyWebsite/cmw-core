<?php

namespace CMW\Model\Pages;

use CMW\Entity\Pages\PageEntity;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Editor\EditorManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\UsersModel;
use JsonException;
use PDOStatement;
use ReflectionException;
use function is_null;
use function mb_strimwidth;

/**
 * Class: @PagesModel
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PagesModel extends AbstractModel
{
    /* => GET */

    public function getPageById(int $id): ?PageEntity
    {
        $cachedData = SimpleCacheManager::getCache("page_id_$id", 'Pages');

        if (!is_null($cachedData)) {
            try {
                return PageEntity::toEntity($cachedData);
            } catch (ReflectionException) {
            }
        }

        $sql = 'SELECT page_id, page_title, page_slug, user_id, page_content, page_state, page_created, page_updated 
                FROM cmw_pages WHERE page_id = :page_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['page_id' => $id])) {
            return null;
        }

        $toReturn = $this->fetchPageResult($res);

        if (!is_null($toReturn)) {
            SimpleCacheManager::storeCache($toReturn->toArray(), "page_id_$id", 'Pages');
        }

        return $toReturn;
    }

    public function getPageBySlug(string $slug): ?PageEntity
    {
        $cachedData = SimpleCacheManager::getCache("page_slug_$slug", 'Pages');

        if (!is_null($cachedData)) {
            try {
                return PageEntity::toEntity($cachedData);
            } catch (ReflectionException) {
            }
        }

        $sql = 'SELECT page_id, page_title, page_slug, user_id, page_content, page_state, 
                page_created, page_updated FROM cmw_pages WHERE page_slug = :page_slug';

        $db = DatabaseManager::getInstance();
        $res = $db->prepare($sql);

        if (!$res->execute(['page_slug' => $slug])) {
            return null;
        }

        $toReturn = $this->fetchPageResult($res);

        if (!is_null($toReturn)) {
            SimpleCacheManager::storeCache($toReturn->toArray(), "page_slug_$slug", 'Pages');
        }

        return $toReturn;
    }

    /**
     * @return PageEntity[]
     */
    public function getPages(): array
    {
        $cachedData = SimpleCacheManager::getCache('pages', 'Pages');

        if (!is_null($cachedData)) {
            try {
                return PageEntity::fromJsonList($cachedData);
            } catch (JsonException|ReflectionException) {
            }
        }

        $sql = 'SELECT page_id FROM cmw_pages';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($page = $res->fetch()) {
            $toReturn[] = $this->getPageById($page['page_id']);
        }

        try {
            SimpleCacheManager::storeCache(PageEntity::toJsonList($toReturn), 'pages', 'Pages');
        } catch (JsonException) {
        }

        return $toReturn;
    }

    /* => CREATE */

    public function createPage(string $title, string $slug, string $content, int $userId, int $state): ?PageEntity
    {
        $data = [
            'page_title' => mb_strimwidth($title, 0, 255),
            'page_slug' => $slug,
            'page_content' => $content,
            'user_id' => $userId,
            'page_state' => $state,
        ];

        $sql = 'INSERT INTO cmw_pages(page_title, page_slug, page_content, user_id, page_state) 
                VALUES (:page_title, :page_slug, :page_content, :user_id, :page_state)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getPageById($id);
        }

        return null;
    }

    /* => DELETE */

    public function deletePage(int $id): bool
    {
        $pageContent = $this->getPageById($id)?->getContent();
        EditorManager::getInstance()->deleteEditorImageInContent($pageContent);

        $var = [
            'page_id' => $id,
        ];
        $sql = 'DELETE FROM cmw_pages WHERE page_id=:page_id';

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute($var);
    }

    /* => UPDATE */

    public function updatePage(int $id, string $slug, string $title, string $content, int $state): ?PageEntity
    {
        $var = [
            'page_id' => $id,
            'page_slug' => $slug,
            'page_title' => mb_strimwidth($title, 0, 255),
            'page_content' => $content,
            'page_state' => $state,
        ];

        $sql = 'UPDATE cmw_pages SET page_slug=:page_slug, page_title=:page_title,
                     page_content=:page_content, page_state=:page_state WHERE page_id=:page_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($var)) {
            return $this->getPageById($id);
        }

        return null;
    }

    private function fetchPageResult(PDOStatement $res): ?PageEntity
    {
        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $user = UsersModel::getInstance()->getUserById($res['user_id']);

        return new PageEntity(
            $res['page_id'],
            $res['page_slug'],
            $res['page_title'],
            $res['page_content'],
            $user,
            $res['page_content'] ?? '',
            $res['page_state'],
            $res['page_created'],
            $res['page_updated'],
        );
    }
}
