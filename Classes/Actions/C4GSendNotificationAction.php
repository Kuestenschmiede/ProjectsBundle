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

/**
 * Class C4GSendNotificationAction
 * @package c4g\projects
 *
 * Versendet E-Mails direkt per Notification Center.
 */
class C4GSendNotificationAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $dlgValues = $this->getPutVars();
        $fieldList = $this->getFieldList();
        $memberId = $dialogParams->getMemberId();
        $brickDatabase = $this->getBrickDatabase();
        $dialogId = $dialogParams->getId();
        $object = $brickDatabase->findByPk($dialogId);

        $notifications = \c4g\projects\C4GBrickDialog::getButtonNotifications(
            \c4g\projects\C4GBrickActionType::ACTION_SEND_NOTIFICATION,
            $dialogParams,$object);
        return $this->sendNotifications($notifications, $dlgValues, $fieldList, $memberId);
    }
}
