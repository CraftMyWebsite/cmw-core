<?php

namespace CMW\Model\Pages;

use CMW\Model\manager;
use CMW\Model\Users\usersModel;
use PDO;

/**
 * Class: @pagesModel
 * @package Pages
 * @author LoGuardiaN | <loguardian@hotmail.com>
 * @version 1.0
 */
class pagesModel extends manager
{
    public int $pageId;
    public int $userId;
    public usersModel $user;
    public string $pageTitle;
    public string $pageSlug;
    public ?string $pageContent = null;
    public string $pageCreated;
    public string $pageUpdated;
    public int $pageState;
    public string $pageContentTranslated;

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

    public function create()
    {
        $var = array(
            "page_slug" => $this->pageSlug,
            "user_id" => $this->userId,
            "page_title" => mb_strimwidth($this->pageTitle, 0, 255),
            "page_content" => $this->pageContent,
            "page_state" => $this->pageState
        );
        $sql = "INSERT INTO cmw_pages(page_slug, user_id, page_title, page_content, page_state) VALUES (:page_slug, :user_id, :page_title, :page_content, :page_state)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $this->pageId = $db->lastInsertId();
            return $this->pageId;
        }

        return -1;
    }

    public function fetch($is_slug = null): void
    {
        $var = $is_slug ? array("page_slug" => $this->pageSlug) : array("page_id" => $this->pageId);

        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, DATE_FORMAT(page_created, '%d/%m/%Y à %H:%i:%s') AS 'page_created', DATE_FORMAT(page_updated, '%d/%m/%Y à %H:%i:%s') AS 'page_updated'"
            . " FROM cmw_pages";
        $sql .= $is_slug ? " WHERE page_slug=:page_slug" : " WHERE page_id=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $result = $req->fetch(PDO::FETCH_ASSOC);

            $this->pageId = $result['page_id'];
            $this->pageSlug = $result['page_slug'];
            $this->pageTitle = $result['page_title'];
            $this->pageContent = $result['page_content'];
            $this->pageCreated = $result['page_created'];
            $this->pageUpdated = $result['page_updated'];
            $this->pageState = $result['page_state'];

            $this->translatePage();

            $user = new usersModel();
            $user->fetch($result['user_id']);
            $this->user = $user;
        }
    }

    public static function fetchAll(): array
    {
        $return = [];

        $sql = "SELECT page_id, page_title, page_slug, user_id, page_content, page_state, DATE_FORMAT(page_created, '%d/%m/%Y à %H:%i:%s') AS 'page_created', DATE_FORMAT(page_updated, '%d/%m/%Y à %H:%i:%s') AS 'page_updated'"
            . " FROM cmw_pages ";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute()) {
            while ($result = $req->fetch()) {
                $pages = new self();
                $pages->pageId = $result['page_id'];
                $pages->pageSlug = $result['page_slug'];
                $pages->pageTitle = $result['page_title'];
                $pages->pageContent = $result['page_content'];
                $pages->pageCreated = $result['page_created'];
                $pages->pageUpdated = $result['page_updated'];
                $pages->pageState = $result['page_state'];

                $pages->translatePage();

                $user = new usersModel();
                $user->fetch($result['user_id']);
                $pages->user = $user;

                $return[] = $pages;

            }
        }

        return $return;
    }

    public function update(): void
    {
        $var = array(
            "page_id" => $this->pageId,
            "page_slug" => $this->pageSlug,
            "page_title" => mb_strimwidth($this->pageTitle, 0, 255),
            "page_content" => $this->pageContent,
            "page_state" => $this->pageState
        );

        $sql = "UPDATE cmw_pages SET "
            . "page_slug" . "=:page_slug,"
            . "page_title" . "=:page_title,"
            . "page_content" . "=:page_content,"
            . "page_state" . "=:page_state"
            . " WHERE " . "page_id" . "=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime();
    }

    public function delete(): void
    {
        $var = array(
            "page_id" => $this->pageId,
        );
        $sql = "DELETE"
            . " FROM cmw_pages"
            . " WHERE page_id=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateEditTime(): void
    {
        $var = array(
            "page_id" => $this->pageId,
        );

        $sql = "UPDATE cmw_pages SET "
            . "page_updated" . "=CURRENT_TIMESTAMP"
            . " WHERE " . "page_id" . "=:page_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function translatePage(): void
    {
        $content = json_decode($this->pageContent, false, 512);
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

        $this->pageContentTranslated = $convertedHtml;
    }
}
