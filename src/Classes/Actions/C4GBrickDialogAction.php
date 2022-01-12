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

abstract class C4GBrickDialogAction extends C4GBrickAction
{
    protected $module = null;

    protected function sendNotifications($notifications, $dlgValues, $fieldList, $memberId, $object = null)
    {
        if ($notifications) {
            if (sizeof($notifications) == 1) {
                $objNotification = \NotificationCenter\Model\Notification::findByPk($notifications);
                if ($objNotification !== null) {
                    $dlgValues['c4g_member_id'] = $memberId;
                    $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, false, $object);
                    $arrTokens['c4g_member_id'] = $memberId;
                    $arrTokens['admin_email'] = $GLOBALS['TL_CONFIG']['adminEmail'];
                    $objNotification->send($arrTokens);

                    return ['usermessage' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION'], 'title' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION_TITLE']];
                }
            } else {
                foreach ($notifications as $notification) {
                    $objNotification = \NotificationCenter\Model\Notification::findByPk($notification);
                    if ($objNotification !== null) {
                        $dlgValues['c4g_member_id'] = $memberId;
                        $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList, false, $object);
                        $arrTokens['c4g_member_id'] = $memberId;
                        $arrTokens['admin_email'] = $GLOBALS['TL_CONFIG']['adminEmail'];
                        $objNotification->send($arrTokens);

                        return ['usermessage' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION'], 'title' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SEND_NOTIFICATION_TITLE']];
                    }
                }
            }
        }

        return ['usermessage' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DONT_SEND_NOTIFICATION'], 'title' => &$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DONT_SEND_NOTIFICATION_TITLE']];
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
