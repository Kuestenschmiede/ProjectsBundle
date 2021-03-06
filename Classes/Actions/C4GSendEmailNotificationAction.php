<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Notifications\C4GBrickNotification;
use MemberModel;

class C4GSendEmailNotificationAction extends C4GBrickDialogAction
{
    private $closeDialog = true;

    public function run()
    {
        $result = false;
        $dialogParams = $this->getDialogParams();
        $putVars = $this->getPutVars();
        $id = $dialogParams->getId();
//        $database = $this->getBrickDatabase();

//        //Todo find a better solution
//        $memberId = -1;
//        $modelClass = $database->getParams()->getModelClass();
//        if ($modelClass && $putVars) {
//          $function = 'getId';
//          $memberId = $modelClass::$function($putVars);
//        }

        $dlgValues = $this->getPutVars();
        $notification_array = unserialize($dialogParams->getNotificationTypeContactRequest());

        $memberId = $dialogParams->getMemberId();
        if ($memberId && $memberId > 0) {
            $table_data_user = MemberModel::findByPk($memberId);
            $dlgValues['user_data_email'] = $table_data_user->email;
            $dlgValues['user_data_firstname'] = $table_data_user->firstname;
            $dlgValues['user_data_lastname'] = $table_data_user->lastname;
        }

        $dlgValues['c4g_member_id'] = $memberId;
        if ($dialogParams->isWithNotification() && $id) {
            if (sizeof($notification_array) == 1) {
                $objNotification = \NotificationCenter\Model\Notification::findByPk($notification_array);
                if ($objNotification !== null) {
                    $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $this->getFieldList(), true);
                    $email_text = $putVars['email_text'];
                    if ($email_text !== ' ') {
                        $arrTokens['email_text'] = $email_text;
                    }
                    $objNotification->send($arrTokens);
                }
            } else {
                foreach ($notification_array as $notification) {
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($notification);
                    if ($objNotification !== null) {
                        $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $this->getFieldList(), true);
                        $email_text = $putVars['email_text'];
                        if ($email_text !== ' ') {
                            $arrTokens['email_text'] = $email_text;
                        }
                        $result = $objNotification->send($arrTokens);
                    }
                }
            }
        }

        if ($this->closeDialog) {
            $dialogParams->setId(-1);
            $action = new C4GCloseDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

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
