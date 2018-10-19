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

use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Module\C4GBrickModuleSaveNotificationsInterface;

class C4GSaveDialogAction extends C4GBrickDialogAction
{
    use C4GTraitCheckMandatoryFields;
    private $withRedirect = false;
    private $andNew = false;
    private $setParentIdAfterSave = false;
    private $setSessionIdAfterInsert = "";

    public function run()
    {
        $dlgValues = $this->getPutVars();
        $fieldList = $this->getFieldList();
        $dialogParams = $this->getDialogParams();
        $viewType = $dialogParams->getViewType();
        $brickDatabase = $this->getBrickDatabase();
        $isPopup = $dialogParams->isPopup();
        $isWithActivationInfo = $dialogParams->isWithActivationInfo();
        $module = $this->getModule();
        $dialogDataObject = $module->getDialogDataObject();

        if ((!$dialogParams->isSaveOnMandatory() || ($dialogParams->isMandatoryCheckOnActivate() && ($dlgValues['published'] === 'true' || $dlgValues['published'] === true))) && !$dialogParams->isSaveWithoutMessages()) {
            $check = $this->checkMandatoryFields($fieldList, $dialogDataObject->getDialogValues());
            if (!empty($check)) {
                return $check;
            }
        }

        if ((!$dialogParams->isSaveOnMandatory() || ($dialogParams->isMandatoryCheckOnActivate() && ($dlgValues['published'] === 'true' || $dlgValues['published'] === true))) && !$dialogParams->isSaveWithoutMessages()) {
            $validate_result = C4GBrickDialog::validateFields($this->makeRegularFieldList($fieldList), $dialogDataObject->getDialogValues());
            if ($validate_result && !$dialogParams->isSaveWithoutMessages()) {
                return array('usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['INVALID_INPUT']);
            }
        }

        $dialogDataObject = $module->getDialogDataObject();
        $dialogDataObject->loadValuesAndAuthenticate();
        $dialogDataObject->setDialogValues($dlgValues);
        $diff = $dialogDataObject->getDifferences();
        if (!$diff->isEmpty()) {
            $dialogDataObject->authenticateAndSaveValues();
        }

        if ($this->module instanceof C4GBrickModuleSaveNotificationsInterface) {
            $this->module->sendSaveNotifications($diff);
        }

        if ((C4GBrickView::isWithoutList($viewType))){
            if ($this->isWithRedirect()) {
                if ($dialogParams->getRedirectSite() && (($jumpTo = \PageModel::findByPk($dialogParams->getRedirectSite())) !== null)) {
                    $return['jump_to_url'] = $jumpTo->getFrontendUrl();
                    return $return;
                }
            }

            if (!$dialogParams->isSaveWithoutMessages() && !$dialogParams->isSaveWithoutSavingMessage()) {
                if ($isPopup) {
                    //return $this->performAction(C4GBrickActionType::ACTION_CLOSEPOPUPDIALOG);
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED']);
                } else {
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SAVED']);
                }
            }
        } else {
            if ($this->isAndNew()) {
                $this->getDialogParams()->setId(-1);
                $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                $action->setModule($this->module);
                $return = $action->run();
            } else {
                if (($dialogParams->isSaveWithoutClose())) {
                    $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $action->setModule($this->module);
                    $return = $action->run();
                } else if (C4GBrickView::isWithoutList($viewType)) {
                    $action = new C4GShowDialogAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $action->setModule($this->module);
                    $return = $action->run();
                } else {
                    $action = new C4GShowListAction($dialogParams, $this->getListParams(), $fieldList, $dlgValues, $brickDatabase);
                    $action->setModule($this->module);
                    $return = $action->run();
                }
            }

            $activation_button = $dialogParams->getButton(C4GBrickConst::BUTTON_ACTIVATION);
            $archive_button = $dialogParams->getButton(C4GBrickConst::BUTTON_ARCHIVE);
            if($activation_button && $archive_button && $activation_button->isEnabled() && $archive_button->isEnabled()){
                if(($dlgValues['published'] == false || $dlgValues['published'] == 'false') && $isWithActivationInfo && (!$dialogParams->isSaveWithoutMessages())){
                    $return['usermessage'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_DATA_NOT_ACTIVATED'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_TITLE_DATA_NOT_ACTIVATED'];
                }
            }
            if (!$dialogParams->isSaveWithoutClose() && $module && $module->getDialogChangeHandler()) {
                $module->getDialogChangeHandler()->clearSession($module->getBrickKey());
            }

            if ($dialogParams->isShowSuccessfullySavedMessage() && !$diff->isEmpty()) {
                if (!$return['usermessage'] && !$return['title']) {
                    $return['usermessage'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED_TITLE'];
                }
            } elseif ($dialogParams->isShowSuccessfullySavedMessage()) {
                if (!$return['usermessage'] && !$return['title']) {
                    $return['usermessage'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED_NO_NEW_DATA'];
                    $return['title'] = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_SUCCESSFULLY_SAVED_NO_NEW_DATA_TITLE'];
                }
            }

            return $return;
        }

        return '';

    }

    /**
     * @return boolean
     */
    public function isWithRedirect()
    {
        return $this->withRedirect;
    }

    /**
     * @param bool $withRedirect
     * @return $this
     */
    public function setWithRedirect($withRedirect = true)
    {
        $this->withRedirect = $withRedirect;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isAndNew()
    {
        return $this->andNew;
    }

    /**
     * @param bool $andNew
     * @return $this
     */
    public function setAndNew($andNew = true)
    {
        $this->andNew = $andNew;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSetParentIdAfterSave()
    {
        return $this->setParentIdAfterSave;
    }

    /**
     * @param bool $setParentIdAfterSave
     * @return $this
     */
    public function setSetParentIdAfterSave($setParentIdAfterSave = true)
    {
        $this->setParentIdAfterSave = $setParentIdAfterSave;
        return $this;
    }

    /**
     * @return string
     */
    public function getSetSessionIdAfterInsert()
    {
        return $this->setSessionIdAfterInsert;
    }

    /**
     * @param bool $setSessionIdAfterInsert
     * @return $this
     */
    public function setSetSessionIdAfterInsert($setSessionIdAfterInsert = true)
    {
        $this->setSessionIdAfterInsert = $setSessionIdAfterInsert;
        return $this;
    }

    public function isReadOnly()
    {
        return true;
    }

}
