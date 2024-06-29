<?php

namespace CMW\Manager\Notification;

use CMW\Controller\Core\MailController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Webhook\DiscordWebhook;
use CMW\Model\Core\MailModel;
use CMW\Utils\Website;


class NotificationManager extends AbstractManager
{
    /**
     * @param string $title
     * @param string $message
     * @param ?string $url
     * @desc ONLY cmw-admin URL ! like (page/add)
     * @return bool
     */
    public static function notify(string $title, string $message, ?string $url = null): bool
    {
        $packageName = self::detectCallingPackage();
        $refusedPackages = NotificationModel::getInstance()->getRefusedPackages();
        $createSilence = in_array($packageName, $refusedPackages);

        if ($createSilence) {
            if (NotificationModel::getInstance()->createNotification($packageName, $title, $message, $url, 1)) {
                return true;
            } else {
                return false;
            }
        } else {
            if ($notification = NotificationModel::getInstance()->createNotification($packageName, $title, $message, $url, 0)) {
                $showOnDiscord = NotificationModel::getInstance()->getSettingValue("showOnDiscord");
                $webhookDiscord = NotificationModel::getInstance()->getSettingValue("webhookDiscord");
                $sendMail = NotificationModel::getInstance()->getSettingValue("sendMail");
                $mailReceiver = NotificationModel::getInstance()->getSettingValue("mailReceiver");
                if ($showOnDiscord && $webhookDiscord) {
                    DiscordWebhook::createWebhook($webhookDiscord)
                        ->setImageUrl(null)
                        ->setTts(false)
                        ->setTitle("Notification - ". $packageName)
                        ->setTitleLink(Website::getUrl().'cmw-admin/notifications')
                        ->setDescription("### " . $title . "\n". $message)
                        ->setColor('F06E08')
                        ->setFooterText(Website::getWebsiteName())
                        ->setFooterIconUrl(null)
                        ->setAuthorName("")
                        ->setAuthorUrl( null)
                        ->send();
                }
                if ($sendMail && $mailReceiver) {
                    if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
                        MailController::getInstance()->sendMail(
                            $mailReceiver,
                            "Notification - " . $packageName,
                            "Titre : ". $notification->getTitle() . "<br>Message : " . $notification->getMessage() . "<br><a href='".Website::getUrl()."cmw-admin/notifications'>Voir sur le panel</a>");
                    }
                }

                return true;
            }
        }

        return false;
    }

    private static function detectCallingPackage(): ?string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        if (isset($backtrace[1]['file'])) {
            $callerFile = $backtrace[1]['file'];
            $packageDir = EnvManager::getInstance()->getValue('PATH_SUBFOLDER').'App/Package/';

            // Check if the caller file is under a package directory
            if (strpos($callerFile, $packageDir) !== false) {
                // Extract the package name from the path
                $startPos = strpos($callerFile, $packageDir) + strlen($packageDir);
                $endPos = strpos($callerFile, '/', $startPos);
                if ($endPos !== false) {
                    return substr($callerFile, $startPos, $endPos - $startPos);
                } else {
                    return substr($callerFile, $startPos);
                }
            }
        }
        return null;
    }
}