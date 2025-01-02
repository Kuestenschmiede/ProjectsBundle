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

use con4gis\CoreBundle\Classes\C4GVersionProvider;
use con4gis\GroupsBundle\Resources\contao\models\MemberGroupModel;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GConfirmArchiveAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $fieldList = $this->getFieldList();
        $dialogId = $dialogParams->getId();
        $memberId = $dialogParams->getMemberId();
        $groupId = $dialogParams->getGroupId();
        $viewType = $dialogParams->getViewType();
        $sendEMails = $dialogParams->getSendEMails();
        $brickCaption = $dialogParams->getBrickCaption();
        $brickDatabase = $this->getBrickDatabase();

        $object = $brickDatabase->findByPk($dialogId);
        if ($object) {
            $notifications = C4GBrickDialog::getButtonNotifications(
                C4GBrickActionType::ACTION_ARCHIVEDIALOG,
                $dialogParams, $object);

            $object->published = false;
            $object->save();

            if (($viewType == C4GBrickViewType::MEMBERBOOKING) && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                $group = MemberGroupModel::findByPk($groupId);
                $group->cg_owner_id = null;
                $group->save();

                \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::log($object);
            }

            if ($sendEMails) {
                $recipient = $sendEMails->getRecipient();
                $senderName = C4GBrickCommon::getNameForMember($memberId);
                if (($viewType == C4GBrickViewType::MEMBERBOOKING) && (C4GVersionProvider::isInstalled('con4gis/booking'))) {
                    $senderName = C4GBrickCommon::getNameForMember($memberId) . ' (' . $object->caption . ')';
                }

                $action = new C4GSendEmailAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
                $action->setRecipient($recipient);
                $action->setSenderName($senderName);
                $action->setText($brickCaption . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_ARCHIVED']);
                $action->run();
            } elseif ($dialogParams->isWithNotification()) {
                $this->sendNotifications($notifications, $dlgValues, $fieldList, $memberId, $object);
            }
        }

        $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

        return $action->run();
    }

    public function isReadOnly()
    {
        return false;
    }
}
