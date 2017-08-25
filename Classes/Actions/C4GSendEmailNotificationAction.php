<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

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

        $dlgValues = array();
        $notification_array = unserialize($dialogParams->getNotificationTypeContactRequest());

        if ($id != -1) {
            $table_data_user = \c4g\MemberModel::findByPk($id);
            $dlgValues['user_data_email'] = $table_data_user->email;
            $dlgValues['user_data_firstname'] = $table_data_user->firstname;
            $dlgValues['user_data_lastname'] = $table_data_user->lastname;
        }

        $memberId = $dialogParams->getMemberId();
        $dlgValues['c4g_member_id'] = $memberId;
        if($dialogParams->isWithNotification() && $id) {
            if (sizeof($notification_array) == 1) {
                $objNotification = \NotificationCenter\Model\Notification::findByPk($notification_array);
                if ($objNotification !== null) {
                    $arrTokens = \c4g\projects\C4GBrickNotification::getArrayTokens($dlgValues, $this->getFieldList(), true);
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
                        $arrTokens = \c4g\projects\C4GBrickNotification::getArrayTokens($dlgValues, $this->getFieldList(), true);
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
        } else {
            return $result;
        }
    }

    /**
     * @return boolean
     */
    public function isCloseDialog()
    {
        return $this->closeDialog;
    }

    /**
     * @param boolean $closeDialog
     */
    public function setCloseDialog($closeDialog)
    {
        $this->closeDialog = $closeDialog;
    }

}
