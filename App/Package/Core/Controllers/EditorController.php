<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Utils\Website;

class EditorController extends AbstractController
{

    public static function getInstalledStyles(): array
    {
        $toReturn = array();
        $stylesFolder = 'Admin/Resources/Vendors/Highlight/Style';
        $contentDirectory = array_diff(scandir("$stylesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $style) {
            if(!empty(file_get_contents("$stylesFolder/$style"))) {
                $toReturn[] = $style;
            }
        }
        return $toReturn;
    }

    public static function getCurrentStyle(): string
    {
        return CoreModel::getInstance()->fetchOption("editor_style");
    }


    #[Link("/editor/config", Link::GET, [], "/cmw-admin")]
    private function editorConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.editor.edit");

        $currentStyle = self::getCurrentStyle();
        $installedStyles = self::getInstalledStyles();

        View::createAdminView("Core", "editorConfig")
            ->addVariableList(["currentStyle" => $currentStyle, "installedStyles" => $installedStyles])
            ->view();
    }

    #[Link("/editor/config", Link::POST, [], "/cmw-admin")]
    private function editorConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.editor.edit");

        $style = filter_input(INPUT_POST, "style");

        CoreModel::updateOption("editor_style",$style);

        Website::refresh();
    }

}