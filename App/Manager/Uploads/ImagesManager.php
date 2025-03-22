<?php

namespace CMW\Manager\Uploads;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Uploads\Errors\ImagesConvertedStatus;
use CMW\Manager\Uploads\Errors\ImagesStatus;
use CMW\Manager\Uploads\Format\ImagesFormat;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use function array_key_exists;
use function copy;
use function fclose;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function filesize;
use function finfo_buffer;
use function finfo_file;
use function finfo_open;
use function fopen;
use function fread;
use function fseek;
use function fwrite;
use function getimagesize;
use function imagealphablending;
use function imagecreatefromstring;
use function imagedestroy;
use function imagegif;
use function imagejpeg;
use function imagepalettetotruecolor;
use function imagepng;
use function imagesavealpha;
use function imagewebp;
use function ini_get;
use function is_dir;
use function is_numeric;
use function is_uploaded_file;
use function mb_substr;
use function mkdir;
use function ord;
use function pathinfo;
use function preg_match;
use function random_int;
use function strlen;
use function strtolower;
use function substr;
use function unlink;
use const FILEINFO_MIME_TYPE;
use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;
use const PREG_OFFSET_CAPTURE;
use const SEEK_CUR;

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
            ImagesException::handleImageError(ImagesStatus::ERROR_INVALID_FILE_DEFINITION);
        }

        if (!empty(mb_substr($dirName, -1))) {
            $dirName .= '/';
        }

        if (!self::createDirectory($dirName)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        $filePath = $file['tmp_name'];
        $fileSize = filesize($filePath);
        $fileSize2 = @getimagesize($filePath);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $filePath);

        $maxFileSize = self::getUploadMaxSizeFileSize();

        if (empty($fileSize2) || ($fileSize2[0] === 0) || ($fileSize2[1] === 0 || filesize($filePath) <= 0)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_EMPTY_FILE);
        }

        if ($fileSize > $maxFileSize) {
            ImagesException::handleImageError(ImagesStatus::ERROR_FILE_TOO_LARGE);
        }

        if (!array_key_exists($fileType, self::$allowedTypes)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_FILE_NOT_ALLOWED);
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
            ImagesException::handleImageError(ImagesStatus::ERROR_CANT_MOVE_FILE);
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
            Flash::send(Alert::WARNING, 'Images', LangManager::translate('core.imageManager.error.createFolder'));
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
            ImagesException::handleImageError(ImagesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        $file = file_get_contents($url);

        if ($file === false) {
            ImagesException::handleImageError(ImagesStatus::ERROR_CANT_DOWNLOAD_FILE);
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_buffer($fileInfo, $file);

        if (!array_key_exists($fileType, self::$allowedTypes)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        $fileName = Utils::genId(random_int(15, 35));
        $extension = self::$allowedTypes[$fileType];

        self::$returnName = $fileName . '.' . $extension;

        $newFilePath = $path . self::$returnName;

        if (!file_put_contents($newFilePath, $file)) {
            ImagesException::handleImageError(ImagesStatus::ERROR_CANT_MOVE_FILE);
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
    public static function convertAndUpload(array $file, string $dirName = '', ImagesFormat $targetFormat = ImagesFormat::WEBP, int $quality = 80, bool $keepName = false, string $customName = ''): string
    {
        $originalFileName = self::upload($file, $dirName, $keepName, $customName);

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;
        $originalFilePath = $path . '/' . $originalFileName;

        $originalExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        if (strtolower($originalExtension) === $targetFormat->value) {
            return $originalFileName;
        }

        $newFileName = pathinfo($originalFileName, PATHINFO_FILENAME) . '.' . $targetFormat->value;
        $newFilePath = $path . '/' . $newFileName;

        $image = imagecreatefromstring(file_get_contents($originalFilePath));
        if (!$image) {
            ImagesException::handleConverterError(ImagesConvertedStatus::ERROR_CONVERTING_IMAGE);
            return $originalFileName;
        }

        switch ($targetFormat) {
            case ImagesFormat::JPEG:
            case ImagesFormat::JPG:
                imagejpeg($image, $newFilePath, $quality);
                break;
            case ImagesFormat::PNG:
                imagepng($image, $newFilePath, 9 - (int)($quality / 10));
                break;
            case ImagesFormat::GIF:
                imagegif($image, $newFilePath);
                break;
            case ImagesFormat::WEBP:
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                imagewebp($image, $newFilePath, $quality);
                break;
            case ImagesFormat::ICO:
            case ImagesFormat::SVG:
                ImagesException::handleConverterError(ImagesConvertedStatus::ERROR_UNSUPPORTED_CONVERSION_FORMAT);
                return $originalFileName;
            default:
                ImagesException::handleConverterError(ImagesConvertedStatus::ERROR_INVALID_TARGET_FORMAT);
                return $originalFileName;
        }

        imagedestroy($image);
        self::deleteImage($originalFileName, $dirName);

        if (!file_exists($newFilePath)) {
            ImagesException::handleConverterError(ImagesConvertedStatus::ERROR_SAVING_FILE);
            return '';
        }

        return $newFileName;
    }

}
