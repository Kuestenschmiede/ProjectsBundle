<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

class C4GConfirmActivationAction extends C4GBrickDialogAction
{
    private $module = null;

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
            $notifications = \c4g\projects\C4GBrickDialog::getButtonNotifications(
                \c4g\projects\C4GBrickActionType::ACTION_ACTIVATIONDIALOG,
                $dialogParams,$object);

            $object->published = true;
            $object->save();

            if (($viewType == C4GBrickViewType::MEMBERBOOKING) && ($GLOBALS['con4gis_booking_extension']['installed'])) {
                $group = \c4g\MemberGroupModel::findByPk($groupId);
                $group->cg_owner_id = $object->group_owner_id;
                $group->save();

                \c4g\projects\C4gBookingGroupsModel::log($object);
            }

            if ($sendEMails) {
                $recipient = $sendEMails->getRecipient();
                $senderName = C4GBrickCommon::getNameForMember($memberId);
                if (($viewType == C4GBrickViewType::MEMBERBOOKING) && ($GLOBALS['con4gis_booking_extension']['installed'])) {
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
    }


}
