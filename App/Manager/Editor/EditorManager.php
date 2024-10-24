<?php

namespace CMW\Manager\Editor;

use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Uploads\ImagesManager;

class EditorManager extends AbstractManager
{
    public function deleteEditorImageInContent(string $content): void
    {
        preg_match_all('/<img[^>]+src="\/Public\/Uploads\/Editor\/([^"]+)"/', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $imageName) {
                ImagesManager::deleteImage($imageName, 'Editor');
            }
        }
    }
}