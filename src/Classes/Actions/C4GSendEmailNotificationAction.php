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
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Notifications\C4GBrickNotification;
use Contao\MemberModel;
use Contao\Model;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use Contao\System;


class C4GSendEmailNotificationAction extends C4GBrickDialogAction
{
    private $closeDialog = true;

    public function run()
    {
        $result = false;
        $dialogParams = $this->getDialogParams();
        $putVars = $this->getPutVars();
        $fieldList = $this->getFieldList();
        $id = $dialogParams->getId();

        $dlgValues = $this->getPutVars();
        $notification_array = \Contao\StringUtil::deserialize($dialogParams->getNotificationTypeContactRequest());

        $memberId = $dialogParams->getMemberId();
        if ($memberId && $memberId > 0) {
            $table_data_user = MemberModel::findByPk($memberId);
            $dlgValues['user_data_email'] = $table_data_user->email;
            $dlgValues['user_data_firstname'] = $table_data_user->firstname;
            $dlgValues['user_data_lastname'] = $table_data_user->lastname;
        }

        $dlgValues['c4g_member_id'] = $memberId;
        if ($dialogParams->isWithNotification() && $id) {
            if (is_array($notification_array) && count($notification_array) == 1) {
                // $objNotification = NotificationCenter::findByPk($notification_array);
                // $parcel = $this->notificationCenter->createParcelForMessage($message->id, $this->notificationCenter->createBasicStampsForNotification($message->pid, $tokens));
                // $result = $this->notificationCenter->sendParcel($parcel);
                // $result = $this->notificationCenter->sendNotification($notificationId, $tokens);
                // if ($objNotification !== null) {

                // System::getContainer()->get('@con4gis\ReservationBundle\Classes\Notifications\C4gNotfificationCenterService')->getNotificationCenter()->sendNotification();
                    $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, true);
                    // $email_text = $putVars['email_text'];
                    // if ($email_text !== ' ') {
                    //     $arrTokens['email_text'] = $email_text;
                    // }
                    // $objNotification->send($arrTokens);
                    $result = System::getContainer()->get('con4gis\ReservationBundle\Classes\Notifications\C4gNotificationCenterService')->getNotificationCenter()->sendNotification(intval($notification_array[0]), $arrTokens);
                // }
            } else {
                foreach ($notification_array as $key=>$notification) {
                    // $objNotification = \NotificationCenter\Model\Notification::findByPk($notification);
                    // $objNotification = NotificationCenter::findByPk($notification);
                    // if ($objNotification !== null) {
                        $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, true);
                        //$email_text = $putVars['email_text'];
                        //if ($email_text !== ' ') {
                        //    $arrTokens['email_text'] = $email_text;
                        // }
                        // $result = $objNotification->send($arrTokens);
                        // $result = $this->notificationCenter->sendNotification($id, $arrTokens);
                        $result = System::getContainer()->get('con4gis\ReservationBundle\Classes\Notifications\C4gNotificationCenterService')->getNotificationCenter()->sendNotification($notification_array[$key], $arrTokens);
                    // }
                }
            }
        }

        if ($this->closeDialog) {
            $dialogParams->setId(-1);
            $action = new C4GCloseDialogAction($dialogParams, $this->getListParams(), $fieldList, $this->getPutVars(), $this->getBrickDatabase());

            return $action->run();
        }

        return $result;
    }

    /**
     * @return boolean
     */
    public function isCloseDialog()
    {
        return $this->closeDialog;
    }

    /**
     * @param bool $closeDialog
     * @return $this
     */
    public function setCloseDialog($closeDialog = true)
    {
        $this->closeDialog = $closeDialog;

        return $this;
    }

    public function isReadOnly()
    {
        return false;
    }
}
