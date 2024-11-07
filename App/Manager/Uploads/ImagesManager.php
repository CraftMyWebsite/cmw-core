<?php

namespace CMW\Manager\Uploads;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use RuntimeException;

class ImagesManager
{
    protected static string $returnName;

    private static array $allowedTypes = [
        'image/png' => 'png',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpeg',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        'image/x-tga' => 'ico',
        'image/svg+xml' => 'svg',
    ];

    /**
     * @param array $files
     * @param string $dirName
     * @return array
     *
     * * @desc Upload images on the uploads' folder. Files accepted [png, jpeg, jpg, gif, webp].
     */
    public static function uploadMultiple(array $files, string $dirName = ''): array
    {
        $toReturn = [];

        foreach ($files as $file) {
            self::upload($file, $dirName);
            $toReturn[] .= self::$returnName;
        }

        return $toReturn;
    }

    /**
     * @param array $file
     * @param string $dirName
     * @param bool $keepName
     * @param string $customName
     * @return string fileName
     *
     * @desc Upload image on the uploads' folder. Files accepted [png, jpeg, jpg, gif, webp, ico, svg].
     */
    public static function upload(array $file, string $dirName = '', bool $keepName = false, string $customName = ''): string
    {
        if (is_uploaded_file($file['tmp_name']) === false) {
            self::handleImageError(ImagesStatus::ERROR_INVALID_FILE_DEFINITION);
        }

        if (!empty(mb_substr($dirName, -1))) {
            $dirName .= '/';
        }

        if (!self::createDirectory($dirName)) {
            self::handleImageError(ImagesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            self::handleImageError(ImagesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        $filePath = $file['tmp_name'];
        $fileSize = filesize($filePath);
        $fileSize2 = @getimagesize($filePath);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $filePath);

        $maxFileSize = self::getUploadMaxSizeFileSize();

        if (empty($fileSize2) || ($fileSize2[0] === 0) || ($fileSize2[1] === 0 || filesize($filePath) <= 0)) {
            self::handleImageError(ImagesStatus::ERROR_EMPTY_FILE);
        }

        if ($fileSize > $maxFileSize) {
            self::handleImageError(ImagesStatus::ERROR_FILE_TOO_LARGE);
        }

        if (!array_key_exists($fileType, self::$allowedTypes)) {
            self::handleImageError(ImagesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        // If $keepName is false, we generate a random name
        if ($keepName) {
            $fileName = $file['name'];
        } elseif (!empty($customName)) {
            $fileName = $customName;
        } else {
            $fileName = Utils::genId(random_int(15, 35));
        }

        $extension = self::$allowedTypes[$fileType];

        self::$returnName = $fileName . '.' . $extension;

        $newFilePath = $path . self::$returnName;

        if (!copy($filePath, $newFilePath)) {
            self::handleImageError(ImagesStatus::ERROR_CANT_MOVE_FILE);
        }

        // Clear image metadata
        $oldFilePath = $path . $fileName . '-old.' . $extension;
        self::clearMetadata($oldFilePath, $path . self::$returnName, $extension);

        // Return the file name with extension
        return self::$returnName;
    }

    /**
     * @param string $dirName
     * @return bool
     * @Desc Create directory on the upload folder
     */
    private static function createDirectory(string $dirName): bool
    {
        if (!file_exists(EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName) && !mkdir($concurrentDirectory = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName, 0777, true) && !is_dir($concurrentDirectory)) {
            Flash::send(Alert::WARNING, 'Dossier', 'Impossible de créer le dossier "%s", vous avez certainement des problème de permissions sur /Uploads', $concurrentDirectory);
            return false;
        }
        return true;
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
     * @param string $oldFilePath
     * @param string $filePath
     * @param string $imageFormat
     * @return void
     * @Desc Clear all the image metadata
     */
    private static function clearMetadata(string $oldFilePath, string $filePath, string $imageFormat): void
    {
        // We copy the current file
        copy($filePath, $oldFilePath);

        $bufferLen = filesize($filePath);
        $fdIn = fopen($oldFilePath, 'rb');
        $fdOut = fopen($filePath, 'wb');

        while (($buffer = fread($fdIn, $bufferLen))) {
            //  \xFF\xE1\xHH\xLLExif\x00\x00 - Exif
            //  \xFF\xE1\xHH\xLLhttp://      - XMP
            //  \xFF\xE2\xHH\xLLICC_PROFILE  - ICC
            //  \xFF\xED\xHH\xLLPhotoshop    - PH
            while (preg_match('/\xFF[\xE1\xE2\xED\xEE](.)(.)(exif|photoshop|http:|icc_profile|adobe)/si', $buffer, $match, PREG_OFFSET_CAPTURE)) {
                $len = ord($match[1][0]) * 256 + ord($match[2][0]);

                fwrite($fdOut, substr($buffer, 0, $match[0][1]));
                $filepos = $match[0][1] + 2 + $len - strlen($buffer);
                fseek($fdIn, $filepos, SEEK_CUR);

                $buffer = fread($fdIn, $bufferLen);
            }
            fwrite($fdOut, $buffer, strlen($buffer));
        }
        fclose($fdOut);
        fclose($fdIn);

        // We delete the "old" file
        unlink($oldFilePath);
    }

    /**
     * @param string $imageName
     * @param string $dirName
     * @return void
     * @desc Delete the specific image
     */
    public static function deleteImage(string $imageName, string $dirName = ''): void
    {
        if (!empty(mb_substr($dirName, -1))) {
            $dirName .= '/';
        }

        if (!file_exists(EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName) && !mkdir($concurrentDirectory = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName) && !is_dir($concurrentDirectory)) {
            Flash::send(Alert::WARNING, 'Dossier', 'Impossible de créer le dossier "%s", vous avez certainement des problème de permissions sur /Uploads', $concurrentDirectory);
            Redirect::redirectPreviousRoute();
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        unlink($path . $imageName);
    }

    /**
     * @return string
     * @desc Return the favicon include
     */
    public static function getFaviconInclude(): string
    {
        $path = EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/Favicon/favicon.ico';

        return '<link rel="icon" type="image/x-icon" href="' . $path . '">';
    }

    public static function downloadFromLink(string $url, string $dirName = ''): string
    {
        if (!empty(mb_substr($dirName, -1))) {
            $dirName .= '/';
        }

        if (!self::createDirectory($dirName)) {
            self::handleImageError(ImagesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            self::handleImageError(ImagesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        $file = file_get_contents($url);

        if ($file === false) {
            self::handleImageError(ImagesStatus::ERROR_CANT_DOWNLOAD_FILE);
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_buffer($fileInfo, $file);

        if (!array_key_exists($fileType, self::$allowedTypes)) {
            self::handleImageError(ImagesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        $fileName = Utils::genId(random_int(15, 35));
        $extension = self::$allowedTypes[$fileType];

        self::$returnName = $fileName . '.' . $extension;

        $newFilePath = $path . self::$returnName;

        if (!file_put_contents($newFilePath, $file)) {
            self::handleImageError(ImagesStatus::ERROR_CANT_MOVE_FILE);
        }

        return self::$returnName;
    }

    /**
     * @desc permet de changer le type d'image, idéal pour les optimiser !
     * @param array $file
     * @param string $dirName
     * @param string $targetExtension
     * @param int $quality
     * @param bool $keepName
     * @param string $customName
     * @return string
     */
    public static function convertAndUpload(array $file, string $dirName = '', string $targetExtension = 'webp', int $quality = 80, bool $keepName = false, string $customName = ''): string
    {
        $originalFileName = self::upload($file, $dirName, $keepName, $customName);

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;
        $originalFilePath = $path . '/' . $originalFileName;

        $originalExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        if (strtolower($originalExtension) === strtolower($targetExtension)) {
            return $originalFileName;
        }

        $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $targetExtension;
        $newFilePath = $path . '/' . $newFileName;

        $image = imagecreatefromstring(file_get_contents($originalFilePath));
        if (!$image) {
            self::handleConverterError(ImagesConvertedStatus::ERROR_CONVERTING_IMAGE);
            return $originalFileName;
        }

        switch ($targetExtension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, $newFilePath, $quality);
                break;
            case 'png':
                imagepng($image, $newFilePath, 9 - (int)($quality / 10));
                break;
            case 'gif':
                imagegif($image, $newFilePath);
                break;
            case 'webp':
                imagewebp($image, $newFilePath, $quality);
                break;
            case 'ico':
            case 'svg':
                self::handleConverterError(ImagesConvertedStatus::ERROR_UNSUPPORTED_CONVERSION_FORMAT);
                return $originalFileName;
            default:
                self::handleConverterError(ImagesConvertedStatus::ERROR_INVALID_TARGET_FORMAT);
                return $originalFileName;
        }

        imagedestroy($image);
        self::deleteImage($originalFileName, $dirName);

        if (!file_exists($newFilePath)) {
            self::handleConverterError(ImagesConvertedStatus::ERROR_SAVING_FILE);
            return '';
        }

        return $newFileName;
    }

    private static function handleConverterError(ImagesConvertedStatus $status): void
    {
        $message = match($status) {
            ImagesConvertedStatus::ERROR_SAVING_FILE => 'Erreur lors de la sauvegarde de l\'image.',
            ImagesConvertedStatus::ERROR_INVALID_TARGET_FORMAT => 'Format cible invalide. Original conservé !',
            ImagesConvertedStatus::ERROR_UNSUPPORTED_CONVERSION_FORMAT => 'Format de conversion non supporté, Original conservé !',
            ImagesConvertedStatus::ERROR_CONVERTING_IMAGE => 'Erreur lors de la conversion de l\'image. Original conservé !',
        };

        Flash::send(Alert::INFO, 'Images Converter', $message);
    }

    #[NoReturn] private static function handleImageError(ImagesStatus $status): void
    {
        $message = match($status) {
            ImagesStatus::ERROR_INVALID_FILE_DEFINITION => 'Cette extension n\'est pas prise en charge !',
            ImagesStatus::ERROR_FOLDER_DONT_EXIST => 'Dossier cible introuvable',
            ImagesStatus::ERROR_EMPTY_FILE => 'Aucune image envoyé !',
            ImagesStatus::ERROR_FILE_TOO_LARGE => 'Image trop volumineuse !',
            ImagesStatus::ERROR_FILE_NOT_ALLOWED => 'Type de document non autorisé !',
            ImagesStatus::ERROR_CANT_MOVE_FILE => 'Impossible de déplacer l\'image.',
            ImagesStatus::ERROR_CANT_DOWNLOAD_FILE => 'Impossible télécharger l\'image.',
            ImagesStatus::ERROR_CANT_CREATE_FOLDER => 'Impossible de créer le dossier cible, problème de permission sur Public/Uploads',
        };

        Flash::send(Alert::ERROR, 'Images', $message);
        Redirect::redirectPreviousRoute();
    }
}
