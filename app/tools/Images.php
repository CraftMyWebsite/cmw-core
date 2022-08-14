<?php

namespace CMW\Utils;

use Exception;
use RuntimeException;

class Images
{
    protected static string $returnName;

    /**
     * @param array $files
     * @param string $dirName
     * @return array
     *
     * * @desc Upload images on the uploads' folder. File accepted [png, jpeg, jpg, gif, webp].
     * @throws Exception
     */
    public static function uploadMultiple(array $files, string $dirName = ""): array
    {
        $toReturn = array();

        foreach ($files as $file) {
            self::upload($file, $dirName);
            $toReturn[] .= self::$returnName;
        }

        return $toReturn;
    }

    /**
     * @param array $file
     * @param string $dirName
     * @return string fileName
     *
     * @desc Upload image on the uploads' folder. File accepted [png, jpeg, jpg, gif, webp, ico, svg].
     * @throws Exception
     */
    public static function upload(array $file, string $dirName = ""): string
    {

        if (is_uploaded_file($file['tmp_name']) === false) //TODO implements error managements
            return "ERROR_INVALID_FILE_DEFINITION";


        if (!empty(mb_substr($dirName, -1)))
            $dirName .= "/";


        self::createDirectory($dirName); //Create the directory if this is necessary


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

        $maxFileSize = self::getUploadMaxSizeFileSize();


        if (empty($fileSize2) || ($fileSize2[0] === 0) || ($fileSize2[1] === 0 || filesize($filePath) <= 0)) //TODO implements error managements
            return "ERROR_EMPTY_FILE";

        if ($fileSize > $maxFileSize) //TODO implements error managements
            return "ERROR_FILE_TOO_LARGE";

        if (!array_key_exists($fileType, $allowedTypes)) //TODO implements error managements
            return "ERROR_FILE_NOT_ALLOWED";

        $fileName = Utils::genId(random_int(15, 35));
        $extension = $allowedTypes[$fileType];

        self::$returnName = $fileName . "." . $extension;

        $newFilePath = $path . self::$returnName;


        if (!copy($filePath, $newFilePath)) //TODO implements error managements
            return "ERROR_CANT_MOVE_FILE";

        //Return the file name with extension
        return self::$returnName;
    }

    /**
     * @param string $dirName
     * @return void
     * @Desc Create directory on the upload folder
     */
    private static function createDirectory(string $dirName): void
    {
        if (!file_exists(getenv("DIR") . "public/uploads/" . $dirName) && !mkdir($concurrentDirectory = getenv("DIR") . "public/uploads/" . $dirName) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    /**
     * @return int
     * @desc Return in byte the uploadMaxSizeFileSize value in php.ini
     */
    private static function getUploadMaxSizeFileSize(): int
    {
        $value = ini_get('upload_max_filesize');

        if (is_numeric($value)) {
            return $value;
        }

        $valueLength = strlen($value);
        $qty = substr($value, 0, $valueLength - 1);
        $unit = strtolower(substr($value, $valueLength - 1));
        $qty *= match ($unit) {
            'k' => 1024,
            'm' => 1048576,
            'g' => 1073741824,
        };
        return $qty;
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
            if (!mkdir($concurrentDirectory = getenv("DIR") . "public/uploads/" . $dirName) && !is_dir($concurrentDirectory)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
            }


        $path = getenv("DIR") . "public/uploads/" . $dirName;

        unlink($path . $imageName);
    }
}
