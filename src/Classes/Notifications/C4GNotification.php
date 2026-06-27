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
        // Don't convert to empty string here, let the Gateway handle it if needed
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

    public function send(array $notificationIds, string $language = '')
    {
        \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', 'Send started for IDs: ' . implode(',', $notificationIds));
        
        $adminEmail = \Contao\Config::get('adminEmail') ?: ($GLOBALS['TL_CONFIG']['adminEmail'] ?? '');
        // Fallback for admin_email if it's empty or still the placeholder string
        if (isset($this->tokens['admin_email'])) {
            if ($this->tokens['admin_email'] === '' || $this->tokens['admin_email'] === '##admin_email##' || $this->tokens['admin_email'] === false || $this->tokens['admin_email'] === ' ') {
                $this->tokens['admin_email'] = $adminEmail;
                \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', "Applied fallback for 'admin_email': " . $this->tokens['admin_email']);
            }
        } else {
            // Force admin_email token even if not defined in constructor
            $this->tokens['admin_email'] = $adminEmail;
            \con4gis\CoreBundle\Resources\contao\models\C4GLogModel::addLogEntry('C4GNotification', "Forced 'admin_email' token: " . $this->tokens['admin_email']);
        }

        foreach ($this->tokens as $key => $token) {
            if (($token === '' || $token === null || $token === false) && !in_array($key, $this->optionalTokens)) {
                // throw new \Exception("C4GNotification: The token '$key' has not been defined.");
                \con4gis\CoreBundle\Resources\contao\models\C4gLogModel::addLogEntry('C4GNotification', "Warning: The token '$key' is empty and not marked as optional. Setting to space string.");
                $this->tokens[$key] = ' ';
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
            $tokens = $this->tokens;
            $adminEmail = $tokens['admin_email'] ?: (\Contao\Config::get('adminEmail') ?: ($GLOBALS['TL_CONFIG']['adminEmail'] ?? ''));
            \con4gis\CoreBundle\Resources\contao\models\C4GLogModel::addLogEntry('C4GNotification', "Final Replacement Admin Email: " . $adminEmail);
            foreach ($tokens as $key => $val) {
                if (is_string($val)) {
                    $tokens[$key] = str_replace('##admin_email##', $adminEmail, $val);
                }
            }

            // Fallback for fields that might contain the placeholder but are not in tokens
            // (e.g. if the notification center configuration has it statically)
            $request = \Contao\System::getContainer()->get('request_stack')->getCurrentRequest();
            if ($request) {
                $request->attributes->set('_c4g_admin_email', $adminEmail);
                foreach ($tokens as $key => $val) {
                    if (is_string($val)) {
                        $tokens[$key] = \Contao\Controller::replaceInsertTags($val);
                        $tokens[$key] = str_replace('##admin_email##', $adminEmail, $tokens[$key]);
                    }
                }
            }

            $stamps = $notificationModel->createBasicStampsForNotification(
                (int)$notificationId,
                $tokens,
            );

            // Re-fetch admin email in case it changed in tokens
            $adminEmail = $tokens['admin_email'] ?: (\Contao\Config::get('adminEmail') ?: ($GLOBALS['TL_CONFIG']['adminEmail'] ?? ''));

            $stampsArr = $stamps->toArray();
            $stampsChanged = false;
            foreach ($stampsArr as $index => $stamp) {
                if (method_exists($stamp, 'getTokens')) {
                    $stampTokens = $stamp->getTokens();
                    $tokensChanged = false;
                    foreach ($stampTokens as $tKey => $tVal) {
                        if (is_string($tVal)) {
                            $replaced = str_replace('##admin_email##', $adminEmail, $tVal);
                            if ($replaced !== $tVal) {
                                $stampTokens[$tKey] = $replaced;
                                $tokensChanged = true;
                            }
                        }
                    }
                    if ($tokensChanged) {
                        $reflection = new \ReflectionClass(get_class($stamp));
                        if ($reflection->hasMethod('withTokens')) {
                            $stampsArr[$index] = $stamp->withTokens($stampTokens);
                            $stampsChanged = true;
                        }
                    }
                }
                
                // Always check getValues/withValues too, as some stamps use it
                if (method_exists($stamp, 'getValues')) {
                    $stampValues = $stamp->getValues();
                    $valuesChanged = false;
                    foreach ($stampValues as $vKey => $vVal) {
                        if (is_string($vVal)) {
                            $replaced = str_replace('##admin_email##', $adminEmail, $vVal);
                            if ($replaced !== $vVal) {
                                $stampValues[$vKey] = $replaced;
                                $valuesChanged = true;
                            }
                        }
                    }
                    if ($valuesChanged) {
                        $reflection = new \ReflectionClass(get_class($stamp));
                        if ($reflection->hasMethod('withValues')) {
                            $stampsArr[$index] = $stamp->withValues($stampValues);
                            $stampsChanged = true;
                        }
                    }
                }
            }

            if ($stampsChanged) {
                $stamps = new \Terminal42\NotificationCenterBundle\Parcel\StampCollection($stampsArr);
            }
            
            // The stamps created above might still contain placeholders in their values 
            // because the Notification Center might have its own logic or fallback.
            // We ensure that the admin_email is replaced in the stamp collection if possible.
            // However, createBasicStampsForNotification already uses our $tokens.
            
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
