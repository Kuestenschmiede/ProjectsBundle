<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
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

    public function setTokenValue(string $token, string $value)
    {
        if (is_string($this->tokens[$token]) === true) {
            $this->tokens[$token] = $value;
        } else {
            throw new \Exception("C4GNotification: Unknown token '$token'.");
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
        foreach ($this->tokens as $key => $token) {
            if ($token === '' && !in_array($key, $this->optionalTokens)) {
                throw new \Exception("C4GNotification: The token '$key' has not been defined.");
            }
        }

        $sendingResult = true;
        $notificationModel = new NotificationCenter();

        //ToDo better solution for files with nc2
        foreach ($this->tokens as $key => $token) {
            if ($key === 'uploadFile') {
                if ($token) {
                    $filePath = C4GUtils::replaceInsertTags("{{file::$token}}");
                    if ($filePath) {
                        $rootDir = \Contao\System::getContainer()->getParameter('kernel.project_dir');
                        $file = $rootDir.'/'.$filePath;
                    }
                    $voucher = $notificationModel->getBulkyItemStorage()->store(
                        FileItem::fromPath($file, basename($file), 'application/pdf', filesize($file))
                    );
                    if ($voucher) {
                        $this->tokens['uploadFile'] = $voucher;
                    }
                }
                break;
            }
        }

        foreach ($notificationIds as $notificationId) {
            $stamps = $notificationModel->createBasicStampsForNotification(
                $notificationId,
                $this->tokens,
            );
            if ($voucher) {
                $stamps = $stamps->with(new BulkyItemsStamp([$voucher]));
                $sendingResult = $notificationModel->sendNotificationWithStamps($notificationId, $stamps) ? true : false;
            } else {
                $sendingResult = $notificationModel->sendNotification($notificationId, $this->tokens, $language) ? true : false;
            }
        }

        return $sendingResult;
    }
}
