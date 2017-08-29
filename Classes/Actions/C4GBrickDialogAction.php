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

abstract class C4GBrickDialogAction extends C4GBrickAction
{
    protected $module = null;

    protected function sendNotifications($notifications, $dlgValues, $fieldList, $memberId, $object = null) {
        if ($notifications) {
            if (sizeof($notifications) == 1) {
                $objNotification = \NotificationCenter\Model\Notification::findByPk($notifications);
                if ($objNotification !== null) {
                    $arrTokens = \c4g\projects\C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, false, $object);
                    $arrTokens['c4g_member_id'] = $memberId;
                    $objNotification->send($arrTokens);
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION_TITLE']);
                }
            } else {
                foreach ($notifications as $notification) {
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($notification);
                    if ($objNotification !== null) {
                        $arrTokens = \c4g\projects\C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, false, $object);
                        $arrTokens['c4g_member_id'] = $memberId;
                        $objNotification->send($arrTokens);
                        return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION_TITLE']);
                    }
                }
            }
        }
        return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DONT_SEND_NOTIFICATION'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DONT_SEND_NOTIFICATION_TITLE']);
    }

    /**
     * @return null
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param null $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }
}
