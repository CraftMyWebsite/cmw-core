<?php

namespace CMW\Model\Pages;

use CMW\Entity\Pages\PageEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Model\Users\UsersModel;
use PDOStatement;

/**
 * Class: @pagesModel
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PagesModel extends DatabaseManager
{

    /*=> GET */

    public function getPageById(int $id): ?PageEntity
    {
        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, 
                DATE_FORMAT(page_created, '%d/%m/%Y à %H:%i:%s') AS 'page_created', 
                DATE_FORMAT(page_updated, '%d/%m/%Y à %H:%i:%s') AS 'page_updated' FROM cmw_pages
                WHERE page_id = :page_id";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array("page_id" => $id))) {
            return null;
        }

        return $this->fetchPageResult($res);
    }

    public function getPageBySlug(string $slug): ?PageEntity
    {
        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, 
                DATE_FORMAT(page_created, '%d/%m/%Y à %H:%i:%s') AS 'page_created', 
                DATE_FORMAT(page_updated, '%d/%m/%Y à %H:%i:%s') AS 'page_updated' FROM cmw_pages
                WHERE page_slug = :page_slug";

        $db = self::getInstance();
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
        $db = self::getInstance();

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

    public function createPage($title, $slug, $content, $userId, $state): ?PageEntity
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

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getPageById($id);
        }

        return null;
    }

    /*=> DELETE */

    public function deletePage($id): bool
    {
        $var = array(
            "page_id" => $id,
        );
        $sql = "DELETE FROM cmw_pages WHERE page_id=:page_id";

        $db = self::getInstance();
        return $db->prepare($sql)->execute($var);
    }

    /*=> UPDATE */

    public function updatePage($id, $slug, $title, $content, $state): ?PageEntity
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

        $db = self::getInstance();
        $req = $db->prepare($sql);
        if ($req->execute($var)) {
            $this->updateEditTime($id);
            return $this->getPageById($id);
        }

        return null;
    }

    public function updateEditTime($id): void
    {
        $var = array(
            "page_id" => $id,
        );

        $sql = "UPDATE cmw_pages SET page_updated = NOW() WHERE page_id=:page_id";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    /* => PRIVATE */

    private function translatePage($content): string
    {
        $content = json_decode($content, false, 512);

        $blocks = $content->blocks;
        $convertedHtml = "";
        foreach ($blocks as $block) {
            switch ($block->type) {
                case "header":
                    $level = $block->data->level;
                    $text = $block->data->text;
                    $convertedHtml .= "<h$level>$text</h$level>";
                    break;

                case "embed":
                    $src = $block->data->embed;
                    $convertedHtml .=
                        <<<HTML
                            <div>
                                <iframe width="560" height="315" src="$src" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                        HTML;
                    break;

                case "paragraph":
                    $text = $block->data->text;
                    $convertedHtml .=
                        <<<HTML
                            <p>$text</p>
                        HTML;
                    break;

                case "delimiter":
                    $convertedHtml .=
                        <<<HTML
                            <br>
                        HTML;
                    break;

                case "image":
                    $src = $block->data->url;
                    $caption = $block->data->caption;
                    $convertedHtml .=
                        <<<HTML
                            <img class="img-fluid" src="$src" title="$caption" alt="$caption" /><br /><em>$caption</em>
                        HTML;
                    break;

                case "list":
                    $convertedHtml .= ($block->data->style === "unordered") ? "<ul>" : "<ol>";
                    foreach ($block->data->items as $item) {
                        $convertedHtml .=
                            <<<HTML
                                <li>$item</li>
                            HTML;
                    }
                    $convertedHtml .= ($block->data->style === "unordered") ? "</ul>" : "</ol>";
                    break;

                case "quote":
                    $text = $block->data->text;
                    $caption = $block->data->caption;
                    $convertedHtml .=
                        <<<HTML
                            <figure>
                                <blockquote>
                                    <p>$text</p> 
                                </blockquote>
                                <figcaption>$caption</figcaption>
                            </figure>
                        HTML;
                    break;

                case "code":
                    $text = $block->data->code;
                    $convertedHtml .=
                        <<<HTML
                            <pre>
                                <code>$text</code>
                            </pre>
                        HTML;
                    break;

                case "warning":
                    $title = $block->data->title;
                    $message = $block->data->message;
                    $convertedHtml .=
                        <<<HTML
                            <div class="warning">
                                <div class="warning-title">
                                    <p>$title</p>
                                </div>
                                <div class="warning-content">
                                    <p>$message</p>
                                </div>
                            </div>
                        HTML;
                    break;

                case "linkTool":
                    $link = $block->data->link;
                    $convertedHtml .=
                        <<<HTML
                            <a href="$link">$link</a>
                        HTML;
                    break;

                case "table":
                    $convertedHtml .= "<table><tbody>";
                    foreach ($block->data->content as $tr) {
                        $convertedHtml .= "<tr>";
                        foreach ($tr as $td) {
                            $convertedHtml .= "<td>$td</td>";
                        }
                        $convertedHtml .= "</tr>";

                    }
                    $convertedHtml .= "</table></tbody>";
                    break;
            }
        }

        return $convertedHtml;
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
            $this->translatePage($res["page_content"]),
            $res["page_state"],
            $res["page_created"],
            $res["page_updated"],
        );

    }
}
