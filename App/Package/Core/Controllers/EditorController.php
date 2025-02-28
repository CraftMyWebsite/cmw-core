<?php
namespace CMW\Controller\Core;

use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Uploads\ImagesManager;
use Exception;

/**
 * Class: @EditorController
 * @package Core
 * @author Zomb
 * @version 0.0.1
 */
class EditorController extends AbstractController
{
    #[Link('/upload/image', Link::POST, [], '/editor', secure: false)]
    private function tinyMceUploadImage(): void
    {
        try {
            $file = $_FILES['file'];
            $uploadedFileName = ImagesManager::convertAndUpload($file, 'Editor');
            $fileUrl = '/Public/Uploads/Editor/' . $uploadedFileName;
            echo json_encode(['location' => $fileUrl]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    #[Link('/upload/noConvert/image', Link::POST, [], '/editor', secure: false)]
    private function tinyMceUploadNoConvertImage(): void
    {
        try {
            $file = $_FILES['file'];
            $uploadedFileName = ImagesManager::upload($file, 'Editor');
            $fileUrl = '/Public/Uploads/Editor/' . $uploadedFileName;
            echo json_encode(['location' => $fileUrl]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}