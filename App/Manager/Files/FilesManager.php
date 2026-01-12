<?php

namespace CMW\Manager\Files;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Files\Errors\FilesStatus;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
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
use function ini_get;
use function is_dir;
use function is_numeric;
use function is_uploaded_file;
use function mb_substr;
use function mkdir;
use function ord;
use function preg_match;
use function random_int;
use function strlen;
use function strtolower;
use function substr;
use function unlink;
use const FILEINFO_MIME_TYPE;
use const PREG_OFFSET_CAPTURE;
use const SEEK_CUR;

class FilesManager
{
    protected static string $returnName;

    // WARN ! : All extensions listed here must be authorized in the .htaccess file located in /Public/Uploads.
    private static array $allowedTypes = [
        // Images
        'image/png' => 'png',
        'image/jpg' => 'jpg',
        'image/jpeg' => 'jpeg',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
        'image/svg+xml' => 'svg',

        // Documents
        'application/pdf' => 'pdf',
        'text/plain' => 'txt',
        'text/csv' => 'csv',

        // Microsoft Office
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',

        // LibreOffice / OpenDocument
        'application/vnd.oasis.opendocument.text' => 'odt',
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
        'application/vnd.oasis.opendocument.presentation' => 'odp',

        // Design / sources graphiques (download only)
        'image/vnd.adobe.photoshop' => 'psd',
        'application/postscript' => 'ai',
        'application/x-indesign' => 'indd',

        // Archives
        'application/zip' => 'zip',
        'application/x-zip-compressed' => 'zip',
        'application/x-tar' => 'tar',
        'application/x-rar' => 'rar',
        'application/vnd.rar' => 'rar',

        // Vidéos
        'video/mp4' => 'mp4',
        'video/webm' => 'webm',
        'video/ogg' => 'ogv',

        // Audio
        'audio/mpeg' => 'mp3',
        'audio/ogg' => 'ogg',
        'audio/wav' => 'wav',
        'audio/webm' => 'weba',
    ];

    /**
     * @param array $files
     * @param string $dirName
     * @return array
     *
     * * @desc Upload files on the uploads' folder.
     */
    public static function uploadMultiple(array $files, string $dirName = ''): array
    {
        $toReturn = [];

        foreach ($files as $file) {
            self::upload($file, $dirName);
            $toReturn[] = self::$returnName;
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
     * @desc Upload files on the uploads' folder.
     */
    public static function upload(array $file, string $dirName = '', bool $keepName = false, string $customName = ''): string
    {
        if (is_uploaded_file($file['tmp_name']) === false) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_DEFINITION);
        }

        // 🔒 Sécurité : anti path traversal
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        if (!self::createDirectory($dirName)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            FilesException::handleFileError(FilesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        $filePath = $file['tmp_name'];
        $fileSize = filesize($filePath);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        $maxFileSize = self::getUploadMaxSizeFileSize();

        if ($fileSize <= 0) {
            FilesException::handleFileError(FilesStatus::ERROR_EMPTY_FILE);
        }

        $isImage = str_starts_with($fileType, 'image/');
        if ($isImage) {
            $img = @getimagesize($filePath);
            if (empty($img) || $img[0] <= 0 || $img[1] <= 0) {
                FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_DEFINITION);
            }
        }

        if ($fileSize > $maxFileSize) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_TOO_LARGE);
        }

        if (!array_key_exists($fileType, self::$allowedTypes)) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        // If $keepName is false, we generate a random name
        if ($keepName) {
            $fileName = $file['name'];
            $fileName = basename($fileName);
            $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
            $fileName = preg_replace('/\.+/', '.', $fileName);
            $fileName = mb_substr($fileName, 0, 120);
            $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        } elseif (!empty($customName)) {
            $fileName = basename($customName);
            $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
            $fileName = preg_replace('/\.+/', '.', $fileName);
            $fileName = mb_substr($fileName, 0, 120);
            $fileName = pathinfo($fileName, PATHINFO_FILENAME);

            if ($fileName === '') {
                $fileName = Utils::genId(random_int(15, 35));
            }
        } else {
            $fileName = Utils::genId(random_int(15, 35));
        }

        $extension = self::$allowedTypes[$fileType];

        self::$returnName = $fileName . '.' . $extension;
        $newFilePath = $path . self::$returnName;

        if ($keepName) {
            $i = 1;
            while (file_exists($newFilePath)) {
                self::$returnName = $fileName . ' (' . $i . ').' . $extension;
                $newFilePath = $path . self::$returnName;
                $i++;
            }
        } else {
            while (file_exists($newFilePath)) {
                $fileName = $fileName . '-' . Utils::genId(6);
                self::$returnName = $fileName . '.' . $extension;
                $newFilePath = $path . self::$returnName;
            }
        }

        if (!move_uploaded_file($filePath, $newFilePath)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
        }

        // Clear image metadata
        if (in_array($extension, ['jpg', 'jpeg'], true)) {
            $oldFilePath = $path . self::$returnName . '.old';
            self::clearMetadata($oldFilePath, $path . self::$returnName);
        }

        // Return the file name with extension
        return self::$returnName;
    }

    public static function uploadAsZip(array $file, string $dirName = '', bool $keepName = false, string $zipName = ''): string
    {
        $dirName = self::normalizeDirName($dirName);

        if (!self::createDirectory($dirName)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;
        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            FilesException::handleFileError(FilesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        // Réutilise ta logique de validation (copie/colle du début de upload)
        if (is_uploaded_file($file['tmp_name']) === false) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_DEFINITION);
        }

        $filePath = $file['tmp_name'];
        $fileSize = filesize($filePath);

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $filePath);
        finfo_close($fileInfo);

        $maxFileSize = self::getUploadMaxSizeFileSize();
        if ($fileSize <= 0) {
            FilesException::handleFileError(FilesStatus::ERROR_EMPTY_FILE);
        }
        if ($fileSize > $maxFileSize) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_TOO_LARGE);
        }
        if (!array_key_exists($fileType, self::$allowedTypes)) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        // Nom du zip
        if ($keepName) {
            $baseZip = self::sanitizeBaseName($file['name']);
        } elseif ($zipName !== '') {
            $baseZip = self::sanitizeBaseName($zipName);
        } else {
            $baseZip = Utils::genId(random_int(15, 35));
        }

        [$zipFileName, $zipFullPath] = self::uniqueFilePath($path, $baseZip, 'zip', $keepName);

        $zip = new \ZipArchive();
        if ($zip->open($zipFullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
        }

        // Nom du fichier à l’intérieur du zip
        $innerBase = self::sanitizeBaseName($file['name']);
        $innerExt = self::$allowedTypes[$fileType];
        $innerName = $innerBase . '.' . $innerExt;

        if (!$zip->addFile($filePath, $innerName)) {
            $zip->close();
            @unlink($zipFullPath);
            FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
        }

        $zip->close();
        self::$returnName = $zipFileName;

        return $zipFileName;
    }

    public static function uploadMultipleAsZip(array $files, string $dirName = '', bool $keepName = false, string $zipName = ''): string
    {
        $dirName = self::normalizeDirName($dirName);

        if (!self::createDirectory($dirName)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        // Nom du zip
        $baseZip = $zipName !== '' ? self::sanitizeBaseName($zipName) : Utils::genId(random_int(15, 35));
        [$zipFileName, $zipFullPath] = self::uniqueFilePath($path, $baseZip, 'zip', $keepName);

        $zip = new \ZipArchive();
        if ($zip->open($zipFullPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
        }

        $usedNames = [];

        foreach ($files as $file) {
            if (is_uploaded_file($file['tmp_name']) === false) {
                $zip->close();
                @unlink($zipFullPath);
                FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_DEFINITION);
            }

            $tmp = $file['tmp_name'];
            $size = filesize($tmp);

            $fi = finfo_open(FILEINFO_MIME_TYPE);
            $type = finfo_file($fi, $tmp);
            finfo_close($fi);

            $max = self::getUploadMaxSizeFileSize();
            if ($size <= 0) {
                $zip->close(); @unlink($zipFullPath);
                FilesException::handleFileError(FilesStatus::ERROR_EMPTY_FILE);
            }
            if ($size > $max) {
                $zip->close(); @unlink($zipFullPath);
                FilesException::handleFileError(FilesStatus::ERROR_FILE_TOO_LARGE);
            }
            if (!array_key_exists($type, self::$allowedTypes)) {
                $zip->close(); @unlink($zipFullPath);
                FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_ALLOWED);
            }

            $innerBase = self::sanitizeBaseName($file['name']);
            $innerExt  = self::$allowedTypes[$type];
            $innerName = $innerBase . '.' . $innerExt;

            // évite doublons dans le zip
            $i = 1;
            $candidate = $innerName;
            while (isset($usedNames[$candidate])) {
                $candidate = $innerBase . ' (' . $i . ').' . $innerExt;
                $i++;
            }
            $usedNames[$candidate] = true;

            if (!$zip->addFile($tmp, $candidate)) {
                $zip->close(); @unlink($zipFullPath);
                FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
            }
        }

        $zip->close();
        self::$returnName = $zipFileName;

        return $zipFileName;
    }

    /**
     * @param string $targetZipFileName
     * @param array|string $files
     * @param string $dirName
     * @param string $zipSubDir
     * @return string
     */
    public static function addToExistingZip(string $targetZipFileName, array|string $files, string $dirName = '', string $zipSubDir = ''): string
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        $targetZipFileName = rawurldecode($targetZipFileName);
        $targetZipFileName = basename($targetZipFileName);

        if (!str_ends_with(strtolower($targetZipFileName), '.zip')) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        $zipSubDir = trim($zipSubDir, '/');
        if ($zipSubDir !== '') {
            $zipSubDir .= '/';
        }

        $basePath = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;
        $zipPath  = $basePath . $targetZipFileName;

        if (!file_exists($zipPath)) {
            FilesException::handleFileError(FilesStatus::ERROR_ZIP_NOT_FOUND);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_OPEN_FILE);
        }

        // Index des noms déjà présents dans le zip (rapide et évite statIndex en boucle)
        $existing = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat && isset($stat['name'])) {
                $existing[$stat['name']] = true;
            }
        }

        // Helper local : renvoie un nom unique dans le zip (file.ext / file (1).ext / ...)
        $makeUnique = static function (string $zipEntryName) use (&$existing): string {
            if (!isset($existing[$zipEntryName])) {
                $existing[$zipEntryName] = true;
                return $zipEntryName;
            }

            $pi = pathinfo($zipEntryName);
            $dir = ($pi['dirname'] ?? '.') !== '.' ? ($pi['dirname'] . '/') : '';
            $base = $pi['filename'] ?? $zipEntryName;
            $ext  = isset($pi['extension']) ? ('.' . $pi['extension']) : '';

            $n = 1;
            do {
                $candidate = $dir . $base . ' (' . $n . ')' . $ext;
                $n++;
            } while (isset($existing[$candidate]));

            $existing[$candidate] = true;
            return $candidate;
        };

        $list = is_array($files) && isset($files['tmp_name']) ? [$files] : (is_array($files) ? $files : [$files]);
        $added = 0;

        foreach ($list as $item) {
            // 1) Cas upload array (tmp_name)
            if (is_array($item) && isset($item['tmp_name'], $item['name'])) {
                if (!is_uploaded_file($item['tmp_name'])) {
                    continue;
                }

                $innerName = basename(rawurldecode($item['name']));
                $innerName = preg_replace('/[^\w.\- ()]/u', '_', $innerName);

                $zipEntryName = $makeUnique($zipSubDir . $innerName);

                if (!$zip->addFile($item['tmp_name'], $zipEntryName)) {
                    $zip->close();
                    FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
                }

                $added++;
                continue;
            }

            // 2) Cas "fichier déjà dans Uploads" (string)
            $file = basename(rawurldecode((string)$item));
            if ($file === '' || str_contains($file, '..')) {
                continue;
            }

            $filePath = $basePath . $file;
            if (!file_exists($filePath)) {
                $zip->close();
                FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_FOUND);
            }

            $zipEntryName = $makeUnique($zipSubDir . $file);

            if (!$zip->addFile($filePath, $zipEntryName)) {
                $zip->close();
                FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
            }

            $added++;
        }

        $zip->close();

        if ($added === 0) {
            FilesException::handleFileError(FilesStatus::ERROR_EMPTY_FILE);
        }

        return $targetZipFileName;
    }

    /**
     * Supprime un fichier (ou plusieurs) dans un ZIP existant.
     * $entries peut être : "file.txt" ou ["file.txt", "dossier/file.txt"]
     *
     * @param string $zipFileName
     * @param string|array $entries
     * @param string $dirName
     * @return string nom du zip
     */
    public static function deleteFromExistingZip(string $zipFileName, string|array $entries, string $dirName = ''): string
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        $zipFileName = rawurldecode($zipFileName);
        $zipFileName = basename($zipFileName);

        if (!str_ends_with(strtolower($zipFileName), '.zip')) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        $basePath = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;
        $zipPath  = $basePath . $zipFileName;

        if (!file_exists($zipPath)) {
            FilesException::handleFileError(FilesStatus::ERROR_ZIP_NOT_FOUND);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== true) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_OPEN_FILE);
        }

        $entries = is_array($entries) ? $entries : [$entries];
        $deleted = 0;

        foreach ($entries as $entry) {
            $entry = rawurldecode((string)$entry);

            // Sécurité : pas de traversal dans le zip
            $entry = str_replace('\\', '/', $entry);
            $entry = ltrim($entry, '/');
            if ($entry === '' || str_contains($entry, '..')) {
                continue;
            }

            if ($zip->locateName($entry) !== false) {
                if (!$zip->deleteName($entry)) {
                    $zip->close();
                    FilesException::handleFileError(FilesStatus::ERROR_CANT_DELETE_FILE);
                }
                $deleted++;
            }
        }

        $zip->close();

        if ($deleted === 0) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_FOUND);
        }

        return $zipFileName;
    }

    /**
     * Renomme un fichier dans Public/Uploads en évitant les collisions:
     * - "monfichier.pdf" -> "monfichier (1).pdf" si déjà pris, etc.
     * Retourne le nouveau nom final (avec extension).
     */
    public static function renameFile(string $oldFileName, string $newBaseName, string $dirName = ''): string
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        $oldFileName = basename($oldFileName);
        if ($oldFileName === '' || str_contains($oldFileName, '..') || str_contains($oldFileName, '\\') || str_contains($oldFileName, '/')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        $oldPath = $path . $oldFileName;
        if (!file_exists($oldPath)) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_FOUND);
        }

        $ext = strtolower(pathinfo($oldFileName, PATHINFO_EXTENSION));
        if ($ext === '') {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_DEFINITION);
        }

        $base = basename($newBaseName);
        $base = preg_replace('/[^a-zA-Z0-9._ -]/', '_', $base);
        $base = preg_replace('/\.+/', '.', $base);
        $base = trim($base);
        $base = mb_substr($base, 0, 120);

        $base = pathinfo($base, PATHINFO_FILENAME);

        if ($base === '') {
            $base = Utils::genId(random_int(15, 35));
        }

        $candidate = $base . '.' . $ext;
        $newPath = $path . $candidate;

        $i = 1;
        while (file_exists($newPath)) {
            if (realpath($newPath) === realpath($oldPath)) {
                return $candidate;
            }

            $candidate = $base . ' (' . $i . ').' . $ext;
            $newPath = $path . $candidate;
            $i++;
        }

        if (!rename($oldPath, $newPath)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
        }

        return $candidate;
    }

    /**
     * @param string $fileName
     * @param string $dirName
     * @return void
     * @desc Delete the specific file
     */
    public static function deleteFile(string $fileName, string $dirName = ''): void
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        $fileName = basename($fileName);
        if ($fileName === '' || str_contains($fileName, '..') || str_contains($fileName, '\\') || str_contains($fileName, '/')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        if (!file_exists(EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName) && !mkdir($concurrentDirectory = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName) && !is_dir($concurrentDirectory)) {
            Flash::send(Alert::WARNING, 'Images', LangManager::translate('core.imageManager.error.createFolder'));
            Redirect::redirectPreviousRoute();
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        unlink($path . $fileName);
    }

    public static function getFileDownloadLink(string $fileName, string $dirName = ''): string
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            return 'ERROR_INVALID_FILE_TARGET';
        }

        $fileName = basename($fileName);
        if ($fileName === '' || str_contains($fileName, '..') || str_contains($fileName, '\\') || str_contains($fileName, '/')) {
            return 'ERROR_INVALID_FILE_TARGET';
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        $diskPath = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName . $fileName;
        if (!file_exists($diskPath)) {
            return 'ERROR_FILE_NOT_FOUND';
        }

        $base = rtrim(EnvManager::getInstance()->getValue('PATH_SUBFOLDER'), '/');

        return $base . '/Public/Uploads/' . $dirName . $fileName;
    }


    public static function downloadFromLink(string $url, string $dirName = ''): string
    {
        if (!str_starts_with($url, 'https://')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_SECURE);
        }

        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }

        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }

        if (!self::createDirectory($dirName)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_CREATE_FOLDER);
        }

        $path = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName;

        if (!empty($dirName) && $dirName !== '/' && !is_dir($path)) {
            FilesException::handleFileError(FilesStatus::ERROR_FOLDER_DONT_EXIST);
        }

        $file = file_get_contents($url);

        if ($file === false) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_DOWNLOAD_FILE);
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_buffer($fileInfo, $file);
        finfo_close($fileInfo);

        if (!array_key_exists($fileType, self::$allowedTypes)) {
            FilesException::handleFileError(FilesStatus::ERROR_FILE_NOT_ALLOWED);
        }

        $fileName = Utils::genId(random_int(15, 35));
        $extension = self::$allowedTypes[$fileType];

        self::$returnName = $fileName . '.' . $extension;

        $newFilePath = $path . self::$returnName;

        if (!file_put_contents($newFilePath, $file)) {
            FilesException::handleFileError(FilesStatus::ERROR_CANT_MOVE_FILE);
        }

        return self::$returnName;
    }

    /**
     * @param string $dirName
     * @return bool
     * @Desc Create directory on the upload folder
     */
    public static function createDirectory(string $dirName): bool
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            return false;
        }

        if (!file_exists(EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName) && !mkdir($concurrentDirectory = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/' . $dirName, 0777, true) && !is_dir($concurrentDirectory)) {
            return false;
        }
        return true;
    }

    /**
     * Supprime un dossier dans Public/Uploads (récursif)
     * Exemple : Media/Test/Test1 → supprime uniquement "Test1"
     *
     * @param string $dirName
     * @return bool
     */
    public static function deleteDirectory(string $dirName): bool
    {
        if ($dirName === '' || str_contains($dirName, '..') || str_contains($dirName, '\\'))
        {
            return false;
        }

        $dirName = trim($dirName, '/');

        $basePath = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/';
        $fullPath = $basePath . $dirName;

        if (!is_dir($fullPath) || !str_starts_with(realpath($fullPath), realpath($basePath))) {
            return false;
        }

        self::deleteDirectoryRecursive($fullPath);

        return true;
    }

    // ----- HELPER -----

    /**
     * Suppression récursive d'un dossier
     *
     * @param string $path
     * @return void
     */
    private static function deleteDirectoryRecursive(string $path): void
    {
        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $itemPath = $path . '/' . $item;

            if (is_dir($itemPath)) {
                self::deleteDirectoryRecursive($itemPath);
            } else {
                unlink($itemPath);
            }
        }

        rmdir($path);
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
     * @return void
     * @Desc Clear all the file metadata
     */
    private static function clearMetadata(string $oldFilePath, string $filePath): void
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

    private static function normalizeDirName(string $dirName): string
    {
        if (str_contains($dirName, '..') || str_contains($dirName, '\\')) {
            FilesException::handleFileError(FilesStatus::ERROR_INVALID_FILE_TARGET);
        }
        if ($dirName !== '' && !str_ends_with($dirName, '/')) {
            $dirName .= '/';
        }
        return $dirName;
    }

    private static function sanitizeBaseName(string $name): string
    {
        $name = basename($name);
        $name = preg_replace('/[^a-zA-Z0-9._ -]/', '_', $name);
        $name = preg_replace('/\.+/', '.', $name);
        $name = trim($name);
        $name = mb_substr($name, 0, 120);
        $name = pathinfo($name, PATHINFO_FILENAME);

        if ($name === '') {
            $name = Utils::genId(random_int(15, 35));
        }
        return $name;
    }

    private static function uniqueFilePath(string $path, string $baseName, string $ext, bool $keepNameStyle = false): array
    {
        // retourne [finalName, finalPath]
        $name = $baseName . '.' . $ext;
        $full = $path . $name;

        if ($keepNameStyle) {
            $i = 1;
            while (file_exists($full)) {
                $name = $baseName . ' (' . $i . ').' . $ext;
                $full = $path . $name;
                $i++;
            }
        } else {
            while (file_exists($full)) {
                $baseName = $baseName . '-' . Utils::genId(6);
                $name = $baseName . '.' . $ext;
                $full = $path . $name;
            }
        }

        return [$name, $full];
    }

    private static function getUniqueZipName(\ZipArchive $zip, string $name): string
    {
        $pathInfo = pathinfo($name);
        $base = $pathInfo['filename'];
        $ext  = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        $existing = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat && isset($stat['name'])) {
                $existing[] = $stat['name'];
            }
        }

        if (!in_array($name, $existing, true)) {
            return $name;
        }

        $i = 1;
        do {
            $candidate = $base . ' (' . $i . ')' . $ext;
            $i++;
        } while (in_array($candidate, $existing, true));

        return $candidate;
    }
}
