<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use con4gis\ProjectsBundle\Classes\Notifications\C4GBrickNotification;
use con4gis\Classes\Common\C4GBrickCommon;
use con4gis\Classes\Common\C4GBrickConst;

abstract class C4GBrickDialogAction extends C4GBrickAction
{
    protected $module = null;

    protected function sendNotifications($notifications, $dlgValues, $fieldList, $memberId, $object = null) {
        if ($notifications) {
            if (sizeof($notifications) == 1) {
                $objNotification = \NotificationCenter\Model\Notification::findByPk($notifications);
                if ($objNotification !== null) {
                    $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, false, $object);
                    $arrTokens['c4g_member_id'] = $memberId;
                    $objNotification->send($arrTokens);
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION_TITLE']);
                }
            } else {
                foreach ($notifications as $notification) {
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($notification);
                    if ($objNotification !== null) {
                        $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, false, $object);
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
