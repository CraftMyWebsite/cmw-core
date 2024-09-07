<?php

namespace CMW\Manager\Notification;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Model\Users\Users2FaModel;

/**
 * Class: @NotificationModel
 * @package Notification
 * @author Zomb
 * @version 0.0.1
 */
class NotificationModel extends AbstractModel
{
    public function getNotificationById(int $id): ?NotificationEntity
    {
        $sql = 'SELECT * FROM cmw_notification WHERE notification_id = :notification_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(array('notification_id' => $id))) {
            return null;
        }

        $res = $res->fetch();

        return new NotificationEntity(
            $res['notification_id'],
            $res['notification_package_name'],
            $res['notification_title'],
            $res['notification_message'],
            $res['notification_slug'] ?? null,
            $res['notification_readed'],
            $res['notification_readed_silence'],
            $res['notification_created_at'],
            $res['notification_updated_at']
        );
    }

    /**
     * @return \CMW\Manager\Notification\NotificationEntity []
     */
    public function getAllNotification(): array
    {
        $sql = 'SELECT notification_id FROM cmw_notification ORDER BY notification_created_at DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($notif = $res->fetch()) {
            $toReturn[] = $this->getNotificationById($notif['notification_id']);
        }

        return $toReturn;
    }

    /**
     * @return \CMW\Manager\Notification\NotificationEntity []
     */
    public function getUnreadNotification(): array
    {
        $sql = 'SELECT notification_id FROM cmw_notification WHERE notification_readed = 0 ORDER BY notification_created_at DESC';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($notif = $res->fetch()) {
            $toReturn[] = $this->getNotificationById($notif['notification_id']);
        }

        return $toReturn;
    }

    public function createNotification(string $packageName, string $title, string $message, ?string $slug, int $silence): ?NotificationEntity
    {
        $data = [
            'notification_package_name' => $packageName,
            'notification_title' => $title,
            'notification_message' => $message,
            'notification_slug' => $slug,
            'notification_readed' => $silence,
            'notification_readed_silence' => $silence,
        ];

        $sql = 'INSERT INTO cmw_notification(notification_package_name, notification_title, notification_message, notification_slug, notification_readed, notification_readed_silence)
                VALUES (:notification_package_name, :notification_title, :notification_message, :notification_slug, :notification_readed, :notification_readed_silence)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            $id = $db->lastInsertId();
            return $this->getNotificationById($id);
        }

        return null;
    }

    public function readNotification(int $notificationId): ?NotificationEntity
    {
        $data = ['notification_id' => $notificationId];

        $sql = 'UPDATE cmw_notification SET notification_readed= 1 WHERE notification_id=:notification_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getNotificationById($notificationId);
        }

        return null;
    }

    public function unReadNotification(int $notificationId): ?NotificationEntity
    {
        $data = ['notification_id' => $notificationId];

        $sql = 'UPDATE cmw_notification SET notification_readed= 0 WHERE notification_id=:notification_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($data)) {
            return $this->getNotificationById($notificationId);
        }

        return null;
    }

    public function countUnreadNotification(): int
    {
        $sql = 'SELECT COUNT(*) AS unread_notifications FROM cmw_notification WHERE notification_readed = 0;';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute()) {
            return 0;
        }

        $res = $req->fetch();

        if (!$res) {
            return 0;
        }

        return $res['unread_notifications'] ?? 0;
    }

    /**
     * @param string $name
     * @return string
     * @desc get the selected option
     */
    public function getSettingValue(string $name): string
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT notification_settings_key FROM cmw_notification_settings WHERE notification_settings_name = ?');

        return ($req->execute(array($name))) ? $req->fetch()['notification_settings_key'] : '';
    }

    /**
     * @param string $notification_settings_key
     * @param string $notification_settings_name
     * @return void
     * @desc Edit a setting
     */
    public function updateSetting(string $notification_settings_name, string $notification_settings_key): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_notification_settings SET notification_settings_key= :notification_settings_key WHERE notification_settings_name= :notification_settings_name');
        $req->execute(array('notification_settings_name' => $notification_settings_name, 'notification_settings_key' => $notification_settings_key));
    }

    /**
     * @return array
     */
    public function getRefusedPackages(): array
    {
        $sql = 'SELECT * FROM cmw_notification_refused_package';
        $db = DatabaseManager::getInstance();
        $req = $db->query($sql);

        $res = $req->fetchAll();

        if (!$res) {
            return [];
        }

        $toReturn = [];

        foreach ($res as $notif) {
            $toReturn[] = $notif['notification_package_name'];
        }
        return $toReturn;
    }

    /**
     * @return bool
     */
    public function clearRefusedPackage(): bool
    {
        $sql = 'DELETE FROM cmw_notification_refused_package';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute();
    }

    public function addRefusedPackage($packageName): bool
    {
        $sql = 'INSERT INTO cmw_notification_refused_package (notification_package_name) VALUES (:packageName)';
        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute(['packageName' => $packageName]);
    }
}
