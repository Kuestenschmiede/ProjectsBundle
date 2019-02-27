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

use con4gis\GroupsBundle\Resources\contao\models\MemberGroupModel;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GConfirmActivationAction extends C4GBrickDialogAction
{
    protected $module = null;

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
                C4GBrickActionType::ACTION_ACTIVATIONDIALOG,
                $dialogParams,$object);

            $object->published = true;
            $object->save();

            if (($viewType == C4GBrickViewType::MEMBERBOOKING) && ($GLOBALS['con4gis']['booking']['installed'])) {
                $group = MemberGroupModel::findByPk($groupId);
                $group->cg_owner_id = $object->group_owner_id;
                $group->save();

                \con4gis\BookingBundle\Resources\contao\models\C4gBookingGroupsModel::log($object);
            }

            if ($sendEMails) {
                $recipient = $sendEMails->getRecipient();
                $senderName = C4GBrickCommon::getNameForMember($memberId);
                if (($viewType == C4GBrickViewType::MEMBERBOOKING) && ($GLOBALS['con4gis']['booking']['installed'])) {
                    $senderName = C4GBrickCommon::getNameForMember($memberId).' ('.$object->caption.')';
                }

                $action = new C4GSendEmailAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
                $action->setRecipient($recipient);
                $action->setSenderName($senderName);
                $action->setText($brickCaption.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_REACTIVATED']);
                $action->run();
            } else if ($dialogParams->isWithNotification()) {
                $this->sendNotifications($notifications, $dlgValues, $fieldList, $memberId, $object);
            }
        }

        if ($dialogParams->getRedirectSite() && (($dialogParams->isRedirectWithActivation() && (!($dialogParams->isRedirectWithActivation() && !$dialogParams->isRedirectWithSaving()))))) {
            $action = new C4GRedirectAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        } else {
            $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        }
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
        return $this;
    }

    public function isReadOnly()
    {
        return false;
    }
}
