<?php

namespace CMW\Utils;

class Images
{
    protected static string $returnName;

    /**
     * @param array $file
     * @param string $dirName
     * @return string fileName
     *
     * @desc Upload image on the uploads' folder. File accepted [png, jpeg, jpg, gif, webp, ico, svg].
     */
    public static function upload(array $file, string $dirName = ""): string
    {

        if (is_uploaded_file($file['tmp_name']) === false) //TODO implements error managements
            return "ERROR_INVALID_FILE_DEFINITION";


        if (!empty(mb_substr($dirName, -1)))
            $dirName .= "/";


        if (!file_exists(getenv("DIR") . "public/uploads/" . $dirName))
            mkdir(getenv("DIR") . "public/uploads/" . $dirName);


        $path = getenv("DIR") . "public/uploads/" . $dirName;


        if (!empty($dirName) && $dirName !== "/") {
            if (!is_dir($path)) //TODO implements error managements
                return "ERROR_FOLDER_DONT_EXIST";
        }

        $filePath = $file['tmp_name'];
        $fileSize = filesize($filePath);
        $fileSize2 = @getimagesize($filePath);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $filePath);

        $allowedTypes = [
            'image/png' => 'png',
            'image/jpg' => 'jpg',
            'image/jpeg' => 'jpeg',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/x-icon' => 'ico',
            'image/svg+xml' => 'svg'
        ];

        $maxFileSize = 20971520; // 20 MB


        if (empty($fileSize2) || ($fileSize2[0] === 0) || ($fileSize2[1] === 0 || filesize($filePath) <= 0)) //TODO implements error managements
            return "ERROR_EMPTY_FILE";

        if ($fileSize > $maxFileSize) //TODO implements error managements
            return "ERROR_FILE_TOO_LARGE";

        if (!in_array($fileType, array_keys($allowedTypes))) //TODO implements error managements
            return "ERROR_FILE_NOT_ALLOWED";

        $fileName = Utils::genId(rand(15, 35));
        $extension = $allowedTypes[$fileType];

        Images::$returnName = $fileName . "." . $extension;

        $newFilePath = $path . Images::$returnName;


        if (!copy($filePath, $newFilePath)) //TODO implements error managements
            return "ERROR_CANT_MOVE_FILE";

        //Return the file name with extension
        return Images::$returnName;
    }


    /**
     * @param array $files
     * @param string $dirName
     * @return array
     *
     * * @desc Upload images on the uploads' folder. File accepted [png, jpeg, jpg, gif, webp].
     */
    public static function uploadMultiple(array $files, string $dirName = ""): array
    {
        $toReturn = array();

        foreach ($files as $file) {
            self::upload($file, $dirName);
            $toReturn[] .= Images::$returnName;
        }

        return $toReturn;
    }


    /**
     * @param string $imageName
     * @param string $dirName
     * @return void
     * @desc Delete the specific image
     */
    public static function deleteImage(string $imageName, string $dirName = ""): void
    {
        if (!empty(mb_substr($dirName, -1)))
            $dirName .= "/";


        if (!file_exists(getenv("DIR") . "public/uploads/" . $dirName))
            mkdir(getenv("DIR") . "public/uploads/" . $dirName);


        $path = getenv("DIR") . "public/uploads/" . $dirName;

        unlink($path . $imageName);
    }

}