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
        $this->tokens = [];
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
        $this->tokens[$token] = $value;
    }

    public function getTokenValue(string $token)
    {
        return $this->tokens[$token] ?? null;
    }

    public function setOptionalToken(string $token)
    {
        $this->optionalTokens[] = $token;
    }

    public function setOptionalTokens(array $token)
    {
        $this->optionalTokens = $token;
    }

    public function send(array $notificationIds, string $language = '', array $vouchers = [])
    {
        \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Send started for IDs: ' . implode(',', $notificationIds));
        
        $adminEmail = \Contao\Config::get('adminEmail') ?: ($GLOBALS['TL_CONFIG']['adminEmail'] ?? '');
        
        // Ensure admin_email token is set and valid
        if (!isset($this->tokens['admin_email']) || !$this->tokens['admin_email'] || $this->tokens['admin_email'] === '##admin_email##' || $this->tokens['admin_email'] === ' ') {
            $this->tokens['admin_email'] = $adminEmail;
        }
        $this->tokens['c4g_admin_email'] = $adminEmail;

        foreach ($this->tokens as $key => $token) {
            if (($token === '' || $token === null || $token === false) && !in_array($key, $this->optionalTokens)) {
                $this->tokens[$key] = '';
            }
        }

        // Final cleanup of tokens and recursive replacement of placeholders
        $recursiveReplace = function (&$array) use ($adminEmail, &$recursiveReplace) {
            foreach ($array as $key => &$val) {
                if (is_string($val)) {
                    $val = str_replace(['##admin_email##', '%23%23admin_email%23%23', '##admin_email_url##', '##c4g_admin_email##'], $adminEmail, $val);
                } elseif (is_array($val)) {
                    $recursiveReplace($val);
                }
            }
        };
        $recursiveReplace($this->tokens);

        try {
            $notificationModel = \Contao\System::getContainer()->get(NotificationCenter::class);
        } catch (\Throwable $e) {
            $notificationModel = \Contao\System::getContainer()->get('terminal42_notification_center');
        }

        // Handle file attachments
        foreach ($this->tokens as $key => $token) {
            if ($token && is_string($token)) {
                $fileToStore = null;
                if (in_array($key, self::UUID_FILE_TOKEN)) {
                    $filePath = C4GUtils::replaceInsertTags("{{file::$token}}");
                    if ($filePath) {
                        $fileToStore = \Contao\System::getContainer()->getParameter('kernel.project_dir') . '/' . $filePath;
                    }
                } elseif (in_array($key, self::FILENAME_TOKEN)) {
                    $fileToStore = \Contao\System::getContainer()->getParameter('kernel.project_dir') . '/' . $token;
                }

                if ($fileToStore && file_exists($fileToStore)) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $fileToStore);
                    finfo_close($finfo);
                    $voucher = $notificationModel->getBulkyItemStorage()->store(
                        FileItem::fromPath($fileToStore, basename($fileToStore), $mimeType, (int)filesize($fileToStore))
                    );
                    if ($voucher) {
                        $this->tokens[$key] = $voucher;
                    }
                }
            }
        }

        $sendingResult = false;
        foreach ($notificationIds as $notificationId) {
            if (!$notificationId) continue;
            
            \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', "Attempting to send notification ID $notificationId to Notification Center");
            
            $stamps = $notificationModel->createBasicStampsForNotification((int)$notificationId, $this->tokens);
            
            // Add vouchers if present
            if (!empty($vouchers)) {
                $stamps = $stamps->with(new BulkyItemsStamp($vouchers));
            }

            $receipts = $notificationModel->sendNotificationWithStamps((int)$notificationId, $stamps);
            $success = false;
            foreach ($receipts as $receipt) {
                if ($receipt->wasDelivered()) {
                    $success = true;
                    break;
                }
            }

            if (!$success) {
                \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Notification ' . $notificationId . ' could not be sent.');
            } else {
                \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Successfully sent notification ' . $notificationId);
                $sendingResult = true;
                
                // Update the reservation to mark confirmation as sent if applicable
                if (isset($this->tokens['reservation_id']) && $this->tokens['reservation_id']) {
                    $db = \Contao\Database::getInstance();
                    $db->prepare("UPDATE tl_c4g_reservation SET emailConfirmationSend = '1' WHERE id = ?")
                        ->execute($this->tokens['reservation_id']);
                }
            }
        }

        return $sendingResult;
    }
}
