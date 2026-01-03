<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

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

        $notifications = C4GBrickDialog::getButtonNotifications(
            C4GBrickActionType::ACTION_SEND_NOTIFICATION,
            $dialogParams, $object);

        return $this->sendNotifications($notifications, $dlgValues, $fieldList, $memberId);
    }

    public function isReadOnly()
    {
        return false;
    }
}
