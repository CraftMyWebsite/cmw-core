<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;

class EditorController extends CoreController
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getInstalledStyles(): array
    {
        $toReturn = array();
        $stylesFolder = 'admin/resources/vendors/highlight/style';
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
        return (new CoreModel())->fetchOption("editor_style");
    }


    #[Link("/editor/config", Link::GET, [], "/cmw-admin")]
    public function editorConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.editor.edit");

        $currentStyle = self::getCurrentStyle();
        $installedStyles = self::getInstalledStyles();

        View::createAdminView("core", "editorConfig")
            ->addVariableList(["currentStyle" => $currentStyle, "installedStyles" => $installedStyles])
            ->view();
    }

    #[Link("/editor/config", Link::POST, [], "/cmw-admin")]
    public function editorConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.editor.edit");

        $style = filter_input(INPUT_POST, "style");

        CoreModel::updateOption("editor_style",$style);

        Utils::refreshPage();
        
    }

}