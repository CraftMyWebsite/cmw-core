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

    /* => PRIVATE */


    private function translatePage(string $content): string
    {
        try {
            $content = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $content = "JsonException: $e";
        }

        $blocks = $content->blocks;
        $convertedHtml = "";
        foreach ($blocks as $block) {
            switch ($block->type) {
                case "header":
                    $level = $block->data->level;
                    $text = $block->data->text;
                    $convertedHtml .= "<h$level class='editor_h$level'>$text</h$level>";
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
                            <p class='editor_p'>$text</p>
                        HTML;
                    break;

                case "delimiter":
                    $convertedHtml .=
                        <<<HTML
                            <hr class='editor_hr'>
                        HTML;
                    break;

                case "image":
                    $src = $block->data->file->url;
                    $caption = $block->data->caption;
                    $convertedHtml .=
                        <<<HTML
                            <img class="editor_img" src="$src" title="$caption" alt="$caption" /><br /><em>$caption</em>
                        HTML;
                    break;

                case "list":
                    $convertedHtml .= ($block->data->style === "unordered") ? "<ul class='editor_ul' style='list-style-type: disc'>" : "<ol class='editor_ol' style='list-style-type: decimal'>";
                    foreach ($block->data->items as $item) {
                        $convertedHtml .=
                            <<<HTML
                                <li class='editor_li'>$item</li>
                            HTML;
                    }
                    $convertedHtml .= ($block->data->style === "unordered") ? "</ul>" : "</ol>";
                    break;

                case "quote":
                    $text = $block->data->text;
                    $caption = $block->data->caption;
                    $convertedHtml .=
                        <<<HTML
                            <figure class='editor_figure'>
                                <blockquote class='editor_blockquote'>
                                    <p class='editor_p'>$text</p> 
                                </blockquote>
                                <figcaption class='editor_figcaption'>$caption</figcaption>
                            </figure>
                        HTML;
                    break;



                case "code":
                    $text = $block->data->code;
                    $textconverted = htmlspecialchars($text, ENT_COMPAT);
                    $convertedHtml .=
                        <<<HTML
                        <div class="editor_allcode">
                            <pre class="editor_pre">
                                <code class="editor_code">$textconverted</code>
                            </pre>
                        </div>
                        HTML;
                    break;

                case "warning":
                    $title = $block->data->title;
                    $message = $block->data->message;
                    $convertedHtml .=
                        <<<HTML
                            <div class="editor_warning">
                                <div class="editor_warning-title">
                                    <p class='editor_p'>$title</p>
                                </div>
                                <div class="editor_warning-content">
                                    <p class='editor_p'>$message</p>
                                </div>
                            </div>
                        HTML;
                    break;

                case "linkTool":
                    $link = $block->data->link;
                    $convertedHtml .=
                        <<<HTML
                            <a class='editor_a' href="$link">$link</a>
                        HTML;
                    break;

                case "table":
                    $convertedHtml .= "<table class='editor_table'><tbody class='editor_tbody'>";
                    foreach ($block->data->content as $tr) {
                        $convertedHtml .= "<tr class='editor_tr'>";
                        foreach ($tr as $td) {
                            $convertedHtml .= "<td class='editor_td'>$td</td>";
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
            $this->translatePage($res["page_content"] ?? ""),
            $res["page_state"],
            $res["page_created"],
            $res["page_updated"],
        );

    }
}
