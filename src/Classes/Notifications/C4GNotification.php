<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\ProjectsBundle\Classes\Notifications;

// use NotificationCenter\Model\Notification;
use con4gis\CoreBundle\Classes\C4GUtils;
use Terminal42\NotificationCenterBundle\BulkyItem\FileItem;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use Terminal42\NotificationCenterBundle\Parcel\Stamp\BulkyItemsStamp;

/**
 * Class C4GNotification
 * Class to simplify sending Notifications via the Notification Center
 * @package con4gis\CoreBundle\Classes\Notification
 */
class C4GNotification
{
    protected $tokens;
    protected $optionalTokens = [];

    //ToDo dynamic solution for all modules
    public const UUID_FILE_TOKEN = ['uploadFile'];
    public const FILENAME_TOKEN = ['icsFilename'];

    public function __construct(array $notification)
    {
        foreach ($notification as $key => $value) {
            if (!is_array($value)) {
                throw new \Exception("C4GNotification: Incorrect configuration, '$key' must be an array.");
            }
            foreach ($value as $token) {
                $this->tokens[$token] = '';
            }
        }
    }

    public function setTokenValue(string $token, $value)
    {
        if ($value === null) {
            $value = '';
        }
        if (isset($this->tokens[$token])) {
            $this->tokens[$token] = (string)$value;
        } else {
            // throw new \Exception("C4GNotification: Unknown token '$token'.");
            // If the token is unknown, we just ignore it to avoid crashes if someone tries to set a token that wasn't in the initial config
            $this->tokens[$token] = (string)$value;
        }
    }

    public function setOptionalToken(string $token)
    {
        $this->optionalTokens[] = $token;
    }

    public function setOptionalTokens(array $token)
    {
        $this->optionalTokens = $token;
    }

    public function send(array $notificationIds, string $language = '')
    {
        \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Send started for IDs: ' . implode(',', $notificationIds));
        foreach ($this->tokens as $key => $token) {
            if ($token === '' && !in_array($key, $this->optionalTokens)) {
                // throw new \Exception("C4GNotification: The token '$key' has not been defined.");
                \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', "Warning: The token '$key' is empty and not marked as optional. Setting to empty string.");
                $this->tokens[$key] = '';
            }
        }

        try {
            $notificationModel = \Contao\System::getContainer()->get('con4gis\ReservationBundle\Classes\Notifications\C4gNotificationCenterService')->getNotificationCenter();
        } catch (\Exception $e) {
            $notificationModel = \Contao\System::getContainer()->get(NotificationCenter::class);
        }

        foreach ($this->tokens as $key => $token) {
            if ($token) {
                foreach (C4GNotification::UUID_FILE_TOKEN as $idKey => $fieldName) {
                    if ($key == $fieldName) {
                        $filePath = C4GUtils::replaceInsertTags("{{file::$token}}");
                        if ($filePath) {
                            $rootDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
                            $file = $rootDir . '/' . $filePath;
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $file);
                            finfo_close($finfo);
                            $voucher = $notificationModel->getBulkyItemStorage()->store(
                                FileItem::fromPath($file, basename($file), $mimeType, filesize($file))
                            );
                            if ($voucher) {
                                $this->tokens[$key] = $voucher;
                            }
                        }
                    }
                }
                foreach (C4GNotification::FILENAME_TOKEN as $idKey => $fieldName) {
                    if ($key == $fieldName) {
                        $filePath = $token;
                        if ($filePath) {
                            $rootDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
                            $file = $rootDir . '/' . $filePath;
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $mimeType = finfo_file($finfo, $file);
                            finfo_close($finfo);
                            $voucher = $notificationModel->getBulkyItemStorage()->store(
                                FileItem::fromPath($file, basename($file), $mimeType, filesize($file))
                            );
                            if ($voucher) {
                                $this->tokens[$key] = $voucher;
                            }
                        }
                    }
                }
            }
        }

        foreach ($notificationIds as $notificationId) {
            $stamps = $notificationModel->createBasicStampsForNotification(
                (int)$notificationId,
                $this->tokens,
            );
            if (!empty($voucher)) {
                $stamps = $stamps->with(new BulkyItemsStamp([$voucher]));
            }
            $sendingResult = $notificationModel->sendNotificationWithStamps((int)$notificationId, $stamps) ? true : false;
            if (!$sendingResult) {
                \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Notification ' . $notificationId . ' could not be sent. Check Symfony Messenger/Queue or Mailer settings.');
            }
            \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Sent notification ' . $notificationId . ' with result: ' . ($sendingResult ? 'true' : 'false'));
        }

        return $sendingResult;
    }
}
