<?php

namespace CMW\Model\Pages;

use CMW\Model\manager;
use CMW\Model\Users\usersModel;
use CMW\Entity\Pages\pagesEntity;
use PDO;

/**
 * Class: @pagesModel
 * @package Pages
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class pagesModel extends manager
{

    public static function exist($var, $is_slug = null)
    {
        $var = $is_slug ? array("page_slug" => $var) : array("page_id" => $var);

        $sql = "SELECT COUNT(page_id) as exist"
            . " FROM cmw_pages";

        $sql .= $is_slug ? " WHERE page_slug=:page_slug" : " WHERE page_id=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        return $req->fetchColumn();
    }

    /***
     * @param string $pageSlug
     * @param int $userId
     * @param string $pageTitle
     * @param string $pageContent
     * @param bool $pageState
     * @return int
     */
    public function create(string $pageSlug, int $userId, string $pageTitle, string $pageContent, bool $pageState): int
    {
        $var = array(
            "page_slug" => $pageSlug,
            "user_id" => $userId,
            "page_title" => mb_strimwidth($pageTitle, 0, 255),
            "page_content" => $pageContent,
            "page_state" => $pageState
        );
        $sql = "INSERT INTO cmw_pages(page_slug, user_id, page_title, page_content, page_state) VALUES (:page_slug, :user_id, :page_title, :page_content, :page_state)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return $db->lastInsertId();
        }

        return -1;
    }

    public function getPage($slug): ?pagesEntity
    {

        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, 
                DATE_FORMAT(page_created, '%d/%m/%Y à %H:%i:%s') AS 'page_created', 
                DATE_FORMAT(page_updated, '%d/%m/%Y à %H:%i:%s') AS 'page_updated' FROM cmw_pages WHERE page_slug=:page_slug";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("page_slug" => $slug))) {
            return null;
        }

        $res = $req->fetch(PDO::FETCH_ASSOC);

        //If we don't have the slug in the db we can continue
        if (empty($res)){
            header("location: " . getenv("PATH_SUBFOLDER")); // redirect to the home page
            return null;
        }

            return new pagesEntity(
                        $res['page_id'],
                        $res['user_id'],
                        $res['page_title'],
                        $res['page_slug'],
                        $res['page_content'],
                        $res['page_updated'],
                        $res['page_state'],
                        $this->translatePage($res['page_content']),
                        $res['page_created']
            );

    }

    /***
     * @return \CMW\Entity\Pages\pagesEntity[]
     */
    public function getPages(): array
    {
        $return = array();

        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, 
                    DATE_FORMAT(page_created, '%d/%m/%Y à %H:%i:%s') AS 'page_created', 
                    DATE_FORMAT(page_updated, '%d/%m/%Y à %H:%i:%s') AS 'page_updated' FROM cmw_pages ";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute()) {
            while ($res = $req->fetch()) {
                $return[] = $this->getPage($res['page_slug']);

               $this->translatePage($res['page_content']);

                $user = new usersModel();
                $user->fetch($res['user_id']);
            }
        }

        return $return;
    }

    public function update(string $pageSlug, string $pageTitle, string $pageContent, bool $pageState, int $pageId): void
    {
        $var = array(
            "page_slug" => $pageSlug,
            "page_title" => mb_strimwidth($pageTitle, 0, 255),
            "page_content" => $pageContent,
            "page_state" => $pageState,
            "page_id" => $pageId
        );

        $sql = "UPDATE cmw_pages SET page_slug=:page_slug, page_title=:page_title,
                     page_content=:page_content, page_state=:page_state WHERE page_id=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        var_dump($req->execute($var));

        $this->updateEditTime($pageId);
    }

    public function delete($pageId): void
    {
        $var = array(
            "page_id" => $pageId,
        );
        $sql = "DELETE FROM cmw_pages WHERE page_id=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateEditTime($page_id): void
    {
        $var = array(
            "page_id" => $page_id,
        );

        $sql = "UPDATE cmw_pages SET page_updated = CURRENT_TIMESTAMP WHERE page_id=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function translatePage(string $pageContent): string
    {
        $content = json_decode($pageContent, false, 512);
        $blocks = $content->blocks;
        $convertedHtml = "";
        foreach ($blocks as $block) {
            switch ($block->type) {
                case "header":
                    $level = $block->data->level;
                    $text = $block->data->text;
                    $convertedHtml .= "<h$level>$text</h$level>";
                    break;
                case "embded":
                    $convertedHtml .= "<div><iframe width='560' height='315' src='$block->data->embed' allow='autoplay; encrypted-media' allowfullscreen></iframe></div>";
                    break;
                case "paragraph":
                    $text = $block->data->text;
                    $convertedHtml .= "<p>$text</p>";
                    break;
                case "delimiter":
                    $convertedHtml .= "<hr />";
                    break;
                case "image":
                    $src = $block->data->url;
                    $caption = $block->data->caption;
                    $convertedHtml .= "<img class='img-fluid' src='$src' title='$caption' alt='$caption' /><br /><em>$caption</em>";
                    break;
                case "list":
                    $convertedHtml .= ($block->data->style === "unordered") ? "<ul>" : "<ol>";
                    foreach ($block->data->items as $item) {
                        $convertedHtml .= "<li>$item</li>";
                    }
                    $convertedHtml .= ($block->data->style === "unordered") ? "</ul>" : "</ol>";
                    break;
                case "quote":
                    $text = $block->data->text;
                    $caption = $block->data->caption;
                    $convertedHtml .= "<figure>";
                    $convertedHtml .= "<blockquote><p>$text</p></blockquote>";
                    $convertedHtml .= "<figcaption>$caption</figcaption>";
                    $convertedHtml .= "</figure>";
                    break;
                case "code":
                    $convertedHtml .= "<pre><code>";
                    $convertedHtml .= $block->data->code;
                    $convertedHtml .= "</pre></code>";
                    break;
                case "warning":
                    $title = $block->data->title;
                    $message = $block->data->message;
                    $convertedHtml .= "<div class='warning'>";
                    $convertedHtml .= "<div class='warning-title'><p>$title</p></div>";
                    $convertedHtml .= "<div class='warning-content'>$message</div>";
                    $convertedHtml .= "</div>";
                    break;
                case "linkTool":
                    $link = $block->data->link;
                    $convertedHtml .= "<a href='$link'>$link</a>";
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
        };

        return $convertedHtml;
    }
}
