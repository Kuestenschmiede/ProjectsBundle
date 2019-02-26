<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
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
     * @param $module
     * @return $this|C4GBrickAction
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }
}
