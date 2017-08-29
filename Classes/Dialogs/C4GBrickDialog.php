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

namespace con4gis\ProjectBundle\Classes\Dialogs;

use c4g\C4GHTMLFactory;
use c4g\C4GUtils;
use con4gis\ProjectBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickFieldType;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GCheckboxField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GDateTimeLocationField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GDecimalField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GEmailField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GFileField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GGeopickerField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GGridField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GHeadlineField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GKeyField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GMultiCheckboxField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GNumberField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GPostalField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GRadioGroupField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GSelectField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GTelField;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GUrlField;
use con4gis\ProjectBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectBundle\Classes\Views\C4GBrickViewType;
use Contao\ModuleModel;


//ToDo Klassen weiter auslagern -> siehe bspw. C4gBrickSelectGroupDialog,
//ToDo danach sollte C4GBrickDialog lediglich die Parentklasse sein.

/**
 * Class C4GBrickDialog
 * @package con4gis
 */
class C4GBrickDialog
{
    private $dialogParams = null;

    /**
     * C4GBrickDialog constructor.
     */
    public function __construct($dialogParams)
    {
        $this->dialogParams = $dialogParams;
    }

    /**
     * @return null
     */
    public function getDialogParams()
    {
        return $this->dialogParams;
    }

    /**
     * @param null $dialogParams
     */
    public function setDialogParams($dialogParams)
    {
        $this->dialogParams = $dialogParams;
    }


    /**
     * show dialog
     * @param $dialogParams
     * @return mixed
     */
    protected function show() {}



        //Wird bisher nicht gebraucht.
    /**
     * @return string
     */
    private function getOnChangeText()
    {
        return 'onchange="$(this).addClass("formdata")';
    }

    /**
     * @param $label
     * @return string
     */
    protected static function addC4GLabel($label)
    {
        return '<label class="c4g_label">' . $label . '</label>';
    }

    /**
     * @param $dialogParams
     * @param $field
     * @param $messageTitle
     * @param $confirmAction
     * @param $confirmButtonText
     * @return array
     */
    public static function showC4GSelectDialog(
        $dialogParams,
        C4GBrickField $field,
        $messageTitle,
        $confirmAction,
        $confirmButtonText,
        $cancelAction,
        $cancelButtonText
    ) {
        $dialogId = $dialogParams->getMemberId();

        $view = '<div class="' . C4GBrickConst::CLASS_SELECT_DIALOG . ' ui-widget ui-widget-content ui-corner-bottom">';
        $view .= C4GHTMLFactory::lineBreak() .
            $field->getC4GDialogField(null, null, $dialogParams) . C4GHTMLFactory::lineBreak();

        $GLOBALS['c4g']['brickdialog']['include']['js'][] = 'jQuery(".chzn-select").chosen();';
        foreach ($GLOBALS['c4g']['brickdialog']['include']['js'] as $string) {
            $view .= "<script>jQuery(document).ready(function () { $string })</script>";
        }

        return array
        (
            'dialogtype' => 'html',
            'dialogdata' => $view,
            'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
            (
                'title' => $messageTitle,
                'modal' => true
            )),
            'dialogid' => C4GBrickActionType::IDENTIFIER_SELECT.$dialogId,
            'dialogstate' => C4GBrickActionType::IDENTIFIER_SELECT.':'.$dialogId,
            'dialogbuttons' => array
            (
                array
                (
                    'action' => $confirmAction.':'.$dialogId,
                    'class'  => 'c4gGuiDefaultAction',
                    'type'   => 'send',
                    'text'   => $confirmButtonText,
                )/*,
                array
                (
                    'action' => $cancelAction.':'.$dialogId,
                    'class'  => 'c4gGuiDefaultAction',
                    'type'   => 'send',
                    'text'   => $cancelButtonText,
                ),*/
            )
        );
    }

    /**
     * @param $dialogId
     * @param $dialogClass
     * @param $messageTitle
     * @param $messageText
     * @param $confirmAction
     * @param $confirmButtonText
     * @param $cancelAction
     * @param $cancelButtonText
     * @return array
     */
    public static function showC4GMessageDialog(
        $dialogId,
        $messageTitle,
        $messageText,
        $confirmAction,
        $confirmButtonText,
        $cancelAction,
        $cancelButtonText,
        $dlgValues,
        $additionalField = null
    ) {
        //Werte durchreichen
        $c4g_uploadURL = $dlgValues['c4g_uploadURL'];

        $view = '<div class="' . C4GBrickConst::CLASS_MESSAGE_DIALOG .
            ' ui-widget ui-widget-content ui-corner-bottom">';
        $view .= '<input type="hidden" id="c4g_uploadURL" name="c4g_uploadURL" class="formdata" value="'
            .$c4g_uploadURL.'">';
        $view .= C4GBrickDialog::addC4GLabel($messageText);


        if ($additionalField !== null) {
            $view .= C4GHTMLFactory::lineBreak() . $additionalField;
        }
        return array
        (
            'dialogtype' => 'html',
            'dialogdata' => $view,
            'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
            (
                'title' => $messageTitle,
                'modal' => true
            )),
            'dialogid' => C4GBrickActionType::IDENTIFIER_MESSAGE.$dialogId,
            'dialogstate' => C4GBrickActionType::IDENTIFIER_MESSAGE.':'.$dialogId,
            'dialogbuttons' => array
            (
                array
                (
                    'action' => $confirmAction.':'.$dialogId,
                    'class'  => 'c4gGuiAction',
                    'type'   => 'send',
                    'text'   => $confirmButtonText,
                ),
                array
                (
                    'action' => $cancelAction.':'.$dialogId,
                    'class'  => 'c4gGuiAction',
                    'type'   => 'send',
                    'text'   => $cancelButtonText,
                ),
            )
        );
    }

    public static function buildDialogView(
        $fieldList,
        $database,
        $dataset,
        $content,
        C4GBrickDialogParams $dialogParams
    ) {
        $view = '<div class="' . C4GBrickConst::CLASS_DIALOG .
            ' ui-widget ui-widget-content ui-corner-bottom">'.C4GHTMLFactory::lineBreak();

        $GLOBALS['c4g']['brickdialog']['include']['js'][] = 'jQuery(".chzn-select").chosen();';

        if ($dialogParams->isWithTabContentCheck()) {
            $GLOBALS['c4g']['brickdialog']['include']['js'][] = 'checkC4GTab();';
        }

        foreach ($GLOBALS['c4g']['brickdialog']['include']['js'] as $string) {
                $view .= "<script>jQuery(document).ready(function () { $string })</script>";
        }
        if ($dialogParams->getOnloadScript()) {
            $string = $dialogParams->getOnloadScript();
            $view .= "<script>jQuery(document).ready(function () { $string })</script>";
        }


        $view .= '<input type="hidden" id="c4g_project_id" name="c4g_project_id" class="formdata" value="'.
            $dialogParams->getProjectId().'">';
        $view .= '<input type="hidden" id="c4g_project_uuid" name="c4g_project_uuid" class="formdata" value="'.
            $dialogParams->getProjectUuid().'">';
        $view .= '<input type="hidden" id="c4g_member_id" name="c4g_member_id" class="formdata" value="'.
            $dialogParams->getMemberId().'">';
        $view .= '<input type="hidden" id="c4g_group_id" name="c4g_group_id" class="formdata" value="'.
            $dialogParams->getGroupId().'">';
        $view .= '<input type="hidden" id="c4g_parent_id" name="c4g_parent_id" class="formdata" value="'.
            $dialogParams->getParentId().'">';

        foreach ($fieldList as $field) {
            $isFormField = $field->isFormField();
            $dbValues = $dataset;

            //TODO C4GExternalValueField um solche Sachen zu vereinfachen?
            $extModel = $field->getExternalModel();
            $extCallbackFunction = $field->getExternalCallbackFunction();

            if ($dataset && $field->getExternalModel() && $field->getExternalIdField()) {
                $extIdFieldName = $field->getExternalIdField();
                $extFieldName = $field->getExternalFieldName();
                $extId = $dataset->$extIdFieldName;

                if ($extId && ($extId > 0)) {
                    if ($extFieldName && ($extFieldName != '')) {
                        $extSearchValue = $field->getExternalSearchValue();
                        if ($extSearchValue) {
                            $tableName = $extModel::getTableName();
                            $fieldName = $field->getFieldName();
                            $sortField = $field->getExternalSortField();
                            $dbValues  = $database->prepare(
                                "SELECT * FROM `$tableName` WHERE `$extFieldName`='$extSearchValue' ".
                                "AND `$fieldName`='$extId' ORDER BY `$tableName`.`$sortField` ".
                                "DESC LIMIT 1 "
                            )->execute();
                        } else {
                            $dbValues = $extModel::findBy($extFieldName, $extId);
                        }
                    } else {
                        $dbValues = $extModel::findByPk($extId);
                    }
                }
            }
            if (is_array($dbValues)) {
                $dbValues = C4GBrickCommon::arrayToObject($dbValues);
            }
            if ($extModel && $extCallbackFunction) {
                $dbvalues_result = $extModel::$extCallbackFunction($dbValues, $database, $dialogParams);
                if ($dbvalues_result) {
                    $dbValues = $dbvalues_result;
                }
            }

            $additionalParameters = array();
            $additionalParameters['database'] = $database;

            $additionalParameters['content'] = $content;
            if ($isFormField) {
                $beforeDiv = '';
                $afterDiv = '';
                if ($field->isHidden()) {
                    $beforeDiv = '<div class="c4g_brick_hidden_field" style="display:none">';
                    $afterDiv  = '</div>';
                }
                if ($field instanceof C4GHeadlineField && $dialogParams->isWithNextPrevButtons()) {
                    if ($dialogParams->getTabContentCounter() > 1) {
                        $beforeDiv .= '<button class="c4g_tab_switch_left" onclick="clickPreviousTab(this)" '.
                            'accesskey="z">' . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BACK'] . '</button>';
                    }
                    if ($dialogParams->getTabContentCounter() > 0) {
                        $additionalClass = $dialogParams->getTabContentCounter() == 1 ? 'c4g_tab_switch_first' : '';
                        $beforeDiv .= '<button class="c4g_tab_switch_right '. $additionalClass .
                            '" onclick="clickNextTab(this)" accesskey="w">' .
                            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['NEXT'] . '</button>';
                    }
                }
                $view .= $beforeDiv.$field->getC4GDialogField(
                    $fieldList,
                    $dbValues,
                    $dialogParams,
                    $additionalParameters
                ) .$afterDiv;
            }
        }

        //close the last accordion content
        if ($dialogParams->isAccordion() && $dialogParams->getAccordionCounter() > 0) {
            $view .= '</div>';
        } elseif ($dialogParams->isTabContent() &&
            $dialogParams->getTabContentCounter() > 0 &&
            $dialogParams->isWithNextPrevButtons()) {
            $view .= '<button class="c4g_tab_switch_left c4g_tab_switch_last" '.
                ' onclick="clickPreviousTab(this)" accesskey="z">' .
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BACK'] . '</button><br>';
        } else {
            $view .= '<br>';
        }

        return $view;
    }


    /**
     * @param $dialogClass
     * @param C4GBrickField [] $fieldList
     * @param $dataset
     * @return array
     */
    public static function showC4GDialog(
        $fieldList,
        $database,
        $dataset,
        $content,
        $headtext,
        C4GBrickDialogParams $dialogParams
    ) {
        $brickCaption = $dialogParams->getBrickCaption();
        $view = C4GBrickDialog::buildDialogView($fieldList, $database, $dataset, $content, $dialogParams);

        $viewType = $dialogParams->getViewType();

        $headline =  $brickCaption . ' ' .$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINES_ADD'];
        if ($viewType == C4GBrickViewType::GROUPFORM || C4GBrickViewType::MEMBERFORM || C4GBrickViewType::PUBLICFORM) {
            $headline = $brickCaption;
        }

        if (!$dataset) {
            $titleStr = $headline;
        } else {
            if (C4GBrickView::isWithoutEditing($viewType)) {
                $titleStr = $brickCaption . ' ' .$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINES_SHOW'];
            } else {
                $titleStr = $brickCaption . ' ' .$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINES_EDIT'];
            }
        }

        if ($dialogParams->isWithoutGuiHeader()) {
            $result =  array
            (
                'headline' => C4GHTMLFactory::headline($headtext),
                'dialogtype' => 'html',
                'dialogdata' => $view,
                'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
                (
                    'modal' => true,
                    'embedDialogs' => true,
                )),
                'dialogid' => C4GBrickActionType::IDENTIFIER_DIALOG.$dataset->id,
                'dialogstate' => C4GBrickActionType::IDENTIFIER_DIALOG.':'.$dataset->id,
                'dialogbuttons' => C4GBrickDialog::getDialogButtons($dialogParams, $dataset)
            );
        } else {
            $result = array
            (
                'headline' => C4GHTMLFactory::headline($headtext),
                'dialogtype' => 'html',
                'dialogdata' => $view,
                'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
                (
                    'title' => $titleStr,
                    'modal' => true,
                    'embedDialogs' => true,
                )),
                'dialogid' => C4GBrickActionType::IDENTIFIER_DIALOG . $dataset->id,
                'dialogstate' => C4GBrickActionType::IDENTIFIER_DIALOG . ':' . $dataset->id,
                'dialogbuttons' => C4GBrickDialog::getDialogButtons($dialogParams, $dataset, $dataset)
            );
        }
        return $result;
    }

    /**
     * @param C4GBrickButton $button
     * @param $dialog_id
     * @return array
     */
    private static function addButtonArray(C4GBrickButton $button, $dialog_id)
    {
        $action = $button->getAction(). ':' . $dialog_id;

        $class = 'c4gGuiAction';
        if ($button->isDefaultByEnter()) {
            $class = 'c4gGuiAction c4gGuiDefaultAction';
        }
        if ($button->getAdditionalCssClass()) {
            $class .= ' ' . $button->getAdditionalCssClass();
        }

        return array(
            'action' => $action,
            'class'  => $class,
            'accesskey' => $button->getAccesskey(),
            'type'   => 'send',
            'text'   => $button->getCaption(),
            'notifiaction' => $button->getNotification()
        );
    }

    /**
     * @param $action
     * @param C4GBrickDialogParams $dialogParams
     * @param $dbValues
     * @return mixed|null
     */
    public static function getButtonNotifications($action, C4GBrickDialogParams $dialogParams, $dbValues)
    {

        if ($action && $dialogParams && $dbValues) {
            $buttons = static::getDialogButtons($dialogParams, $dbValues);
            if ($buttons) {
                foreach ($buttons as $button) {
                    if (strpos($button['action'], $action) !== false) {
                        return unserialize($button['notifiaction']);
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param C4GBrickDialogParams $dialogParams
     * @param $dbValues
     * @return array
     */
    private static function getDialogButtons(C4GBrickDialogParams $dialogParams, $dbValues)
    {
        $result = array();

        //SAVE BUTTON
        $type_save = C4GBrickConst::BUTTON_SAVE;
        if (($dialogParams->checkButtonVisibility($type_save) && (!$dialogParams->isFrozen()))) {
            $button_save = $dialogParams->getButton($type_save);
            $result[] = static::addButtonArray($button_save, $dbValues->id);
        }

        $type_save_and_redirect = C4GBrickConst::BUTTON_SAVE_AND_REDIRECT;
        if (($dialogParams->checkButtonVisibility($type_save_and_redirect) && (!$dialogParams->isFrozen()))) {
            $button_save_and_redirect = $dialogParams->getButton($type_save_and_redirect);
            $result[] = static::addButtonArray($button_save_and_redirect, $dbValues->id);
        }
        $type_ticket = C4GBrickConst::BUTTON_TICKET;
        if (($dialogParams->checkButtonVisibility($type_ticket) && (!$dialogParams->isFrozen()))) {
            $button_ticket = $dialogParams->getButton($type_ticket);
            $result[] = static::addButtonArray($button_ticket, $dbValues->id);
        }

        //SAVE & NEW BUTTON
//            $type_save_and_new = C4GBrickConst::BUTTON_SAVE_AND_NEW;
//            if (($dialogParams->checkButtonVisibility($type_save_and_new) && (!$dialogParams->isFrozen()))) {
//                $button_save_and_new = $dialogParams->getButton($type_save_and_new);
//                $result[] = static::addButtonArray($button_save_and_new, $dbValues->id);
//            }

        //BOOKING BUTTON
        $groupKeyField = $dialogParams->getViewParams()->getGroupKeyField();
        if ($dbValues->$groupKeyField && ($dbValues->$groupKeyField > 0)) {
            $type_booking = C4GBrickConst::BUTTON_BOOKING_CHANGE;
        } else {
            $type_booking = C4GBrickConst::BUTTON_BOOKING_SAVE;
        }
        if ($dialogParams->checkButtonVisibility($type_booking)) {
            $button_booking = $dialogParams->getButton($type_booking);
            $result[] = static::addButtonArray($button_booking, $dbValues->id);
        }

        //REDIRECT BUTTON
        $redirect = C4GBrickConst::BUTTON_REDIRECT;
        if ($dialogParams->checkButtonVisibility($redirect, $dbValues)) {
            $button_redirect = $dialogParams->getButton($redirect);
            $result[] = static::addButtonArray($button_redirect, $dbValues->id);
        }

        //CLICK BUTTON
        $click = C4GBrickConst::BUTTON_CLICK;
        if ($dialogParams->checkButtonVisibility($click)) {
            $buttons_click = $dialogParams->getButtonsArray($click);
            if ($buttons_click) {
                foreach ($buttons_click as $button_click) {
                    $result[] = static::addButtonArray($button_click, $dbValues->id);
                }
            }
        }

        //ARCHIVE BUTTON
        if ($dbValues && $dbValues->published) {
            $type_archive = C4GBrickConst::BUTTON_ARCHIVE;
        } elseif ($dbValues || ($dialogParams->isRedirectWithSaving() && $dialogParams->isRedirectWithActivation())) {
            $type_archive = C4GBrickConst::BUTTON_ACTIVATION;
        }
        if ($dialogParams->checkButtonVisibility($type_archive)) {
            $button_archive = $dialogParams->getButton($type_archive);
            $result[] = static::addButtonArray($button_archive, $dbValues->id);
        }

        //FREEZE BUTTON (freeze project)
        if ($dbValues && ($dbValues->is_frozen == true)) {
            $type_freeze = C4GBrickConst::BUTTON_DEFROST;
        } elseif ($dbValues) {
            $type_freeze = C4GBrickConst::BUTTON_FREEZE;
        }
        if ($dialogParams->checkButtonVisibility($type_freeze)) {
            $button_freeze = $dialogParams->getButton($type_freeze);
            $result[] = static::addButtonArray($button_freeze, $dbValues->id);
        }

        //EXPORT BUTTON
        $type_export = C4GBrickConst::BUTTON_EXPORT;
        if ($dialogParams->checkButtonVisibility($type_export)) {
            $button_export = $dialogParams->getButton($type_export);
            $result[] = static::addButtonArray($button_export, $dbValues->id);
        }

        //PRINT BUTTON
        $type_print = C4GBrickConst::BUTTON_PRINT;
        if ($dialogParams->checkButtonVisibility($type_print)) {
            $button_print = $dialogParams->getButton($type_print);
            $result[] = static::addButtonArray($button_print, $dbValues->id);
        }

        //DELETE BUTTON
        $type_delete = C4GBrickConst::BUTTON_DELETE;
        if (($dialogParams->checkButtonVisibility($type_delete) && (!$dialogParams->isFrozen()))) {
            $button_delete = $dialogParams->getButton($type_delete);
            $result[] = static::addButtonArray($button_delete, $dbValues->id);
        }

        //SEND-EMAIL BUTTON
        $send_email = C4GBrickConst::BUTTON_SEND_EMAIL;
        if ($dialogParams->checkButtonVisibility($send_email)) {
            $button_send_email = $dialogParams->getButton($send_email);
            $result[] = static::addButtonArray($button_send_email, $dbValues->id);
        }

        //SEND-NOTIFICATION BUTTON
        $send_notification = C4GBrickConst::BUTTON_SEND_NOTIFICATION;
        if ($dialogParams->checkButtonVisibility($send_notification)) {
            $button_send_notification = $dialogParams->getButton($send_notification);
            $result[] = static::addButtonArray($button_send_notification, $dbValues->id);
        }

        //CLOSE BUTTON
        $type_close = C4GBrickConst::BUTTON_CANCEL;
        if ($dialogParams->checkButtonVisibility($type_close)) {
            $button_close = $dialogParams->getButton($type_close);
            $result[] = static::addButtonArray($button_close, $dbValues->id);
        }

        return $result;
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $dlgValues
     * @return bool
     */
    public static function checkMandatoryFields($fieldList, $dlgValues)
    {
        if (($fieldList) && ($dlgValues)) {
            foreach ($fieldList as $field) {
                if ($field->isMandatory() && $field->isDisplay()) {
                    $fieldName = $field->getFieldName();

                    $fieldData = $dlgValues[$fieldName];
                    if ($field instanceof C4GSelectField) {
                        $conditions = $field->getCondition();
                        if (($conditions) && ($field->getConditionType() != C4GBrickConditionType::BOOLSWITCH)) {
                            $found = false;
                            foreach ($conditions as $condition) {
                                if ($condition->getType() == C4GBrickConditionType::VALUESWITCH) {
                                    $conditionField = $condition->getFieldName();
                                    $conditionValue = $condition->getValue();

                                    $conFieldValue = $dlgValues[$conditionField];
                                    if ($conditionValue == $conFieldValue) {
                                        $found = true;
                                        break;
                                    }
                                }
                            }
                            if (!$found) {
                                continue;
                            }
                        }

                        $additionalId = $field->getAdditionalID();
                        if (!empty($additionalId)) {
                            $fieldData = $dlgValues[$fieldName.'_'.$additionalId];
                        }
                    }

                    if ($field instanceof C4GMultiCheckboxField) {
                        foreach ($dlgValues as $name => $dlgValue) {
                            if (C4GUtils::startsWith($name, $fieldName.'|')) {
                                if ($dlgValue == true && $dlgValue !== 'false') {
                                    $fieldData = $name;
                                    break;
                                }
                            }
                        }
                    }

                    if ($field instanceof C4GRadioGroupField) {
                        $conditions = $field->getCondition();
                        if (($conditions) && ($field->getConditionType() != C4GBrickConditionType::BOOLSWITCH)) {
                            $found = false;
                            foreach ($conditions as $condition) {
                                if ($condition->getType() == C4GBrickConditionType::VALUESWITCH) {
                                    $conditionField = $condition->getFieldName();
                                    $conditionValue = $condition->getValue();

                                    $conFieldValue = $dlgValues[$conditionField];
                                    if ($conditionValue == $conFieldValue) {
                                        $found = true;
                                        break;
                                    }
                                }

                                if ($condition->getType() == C4GBrickConditionType::METHODSWITCH) {
                                    $conditionField = $condition->getFieldName();
                                    $conditionFunction = $condition->getFunction();
                                    $conditionModel = $condition->getModel();

                                    if ($conditionField && $conditionModel && $conditionFunction) {
                                        $conFieldValue = strtotime($dlgValues[$conditionField]);
                                        $found = $conditionModel::$conditionFunction($conFieldValue);
                                        if ($found) {
                                            break;
                                        }
                                    }
                                }
                            }
                            if (!$found) {
                                continue;
                            }
                        }

                        $additionalId = $field->getAdditionalID();
                        if (!empty($additionalId)) {
                            $fieldData = $dlgValues[$fieldName.'_'.$additionalId];
                        }
                    }
                    if ($field instanceof C4GCheckboxField) {
                        $check = C4GBrickCommon::strToBool($dlgValues[$fieldName]);
                        if (!$check) {
                            //published status wird direkt gespeichert.
                            return $field;
                        }
                    }
                    if ($field instanceof C4GGeopickerField) {
                        $loc_geox = $dlgValues['geox'];
                        $loc_geoy = $dlgValues['geoy'];
                        if ($loc_geox && $loc_geoy) {
                            return true;
                        } else {
                            return false;
                        }
                    }

                    if (is_string($fieldData)) {
                        $fieldData = trim($fieldData);
                    }
                    if (($fieldData == null) || ($fieldData) == '') {
                        return $field;
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $dlgValues
     * @param $brickDatabase
     * @param $viewType
     * @return null
     */
    public static function validateUnique($fieldList, $dlgValues, $brickDatabase, $dialogParams)
    {
        $viewType = $dialogParams->getViewType();

        //ToDo check if we need here another function: isWithNotification
        if (C4GBrickView::isWithSaving($viewType)) {
            if (($fieldList) && ($dlgValues)) {
                foreach ($fieldList as $field) {
                    $fieldName = $field->getFieldName();
                    $caption = $field->getTitle();

                    //Für diese Variante muss validateDBUnique aufgerufen werden.
                    if ($field->isDbUnique() && $field->getDbUniqueAdditionalCondition()) {
                        continue;
                    }

                    if ($viewType && (C4GBrickView::isGroupBased($viewType))) {
                        if ($field->isUnique() || $field->isDbUnique()) {
                            $groupId = $dlgValues['c4g_group_id'];
                            $dlgValue = $dlgValues[$fieldName];
                            $dbValues = $brickDatabase->findBy($fieldName, $dlgValue);

                            $groupKeyField = $dialogParams->getViewParams()->getGroupKeyField();

                            if ($dbValues !== null) {
                                foreach ($dbValues as $dbValue) {
                                    $check = $dbValue->$groupKeyField;
                                    if (($groupId) && ($groupId > 0)) {
                                        $set[$groupKeyField] = $groupId;
                                        if (($dbValue->id != \Session::getInstance()->get("c4g_brick_dialog_id"))
                                            && $check == $groupId) {
                                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe']
                                                . ' "' . $caption . '" '
                                            . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe_2'] . $dlgValue;
                                        }
                                    }
                                }
                            }
                        }
                    } elseif ($viewType && (C4GBrickView::isMemberBased($viewType))) {
                        if ($field->isUnique() || $field->isDbUnique()) {
                            $memberId = $dlgValues['c4g_member_id'];
                            $dlgValue = $dlgValues[$fieldName];
                            $dbValues = $brickDatabase->findBy($fieldName, $dlgValue);

                            $memberKeyField = $dialogParams->getViewParams->getMemberKeyField();

                            if ($dbValues !== null) {
                                foreach ($dbValues as $dbValue) {
                                    $check = $dbValue->$memberKeyField;
                                    if (($dbValue->id != \Session::getInstance()->get("c4g_brick_dialog_id"))
                                        && ($memberId) && ($memberId > 0)) {
                                        $set[$memberKeyField] = $memberId;
                                        if ($check == $memberId) {
                                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe']
                                                . ' "' . $caption . '" '
                                            . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe_2'] . $dlgValue;
                                        }
                                    }
                                }
                            }
                        }
                    } elseif ($viewType && (C4GBrickView::isPublicBased($viewType))) {
                        if ($field->isUnique() || $field->isDbUnique()) {
                            $dlgValue = $dlgValues[$fieldName];
                            $dbValues = $brickDatabase->findBy($fieldName, $dlgValue);
                            $additionalId       = $dialogParams->getAdditionalId();
                            $additionalIdField  = $dialogParams->getAdditionalIdField();
                            if ($additionalId && $additionalIdField) {
                                if ($dbValues !== null) {
                                    foreach ($dbValues as $dbValue) {
                                        $check = $dbValue->$additionalIdField;
                                        if (($dbValue->id != \Session::getInstance()->get("c4g_brick_dialog_id"))) {
                                            if ($check == $additionalId) {
                                                return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe']
                                                    . ' "' . $caption . '" '
                                                . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe_2'] . $dlgValue;
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($dbValues !== null) {
                                    foreach ($dbValues as $dbValue) {
                                        if (($dbValue->id != \Session::getInstance()->get("c4g_brick_dialog_id"))) {
                                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe']
                                                . ' "' . $caption . '" '
                                            . $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_uniqe_2'] . $dlgValue;
                                        }
                                    }
                                }

                            }
                        }
                    }
                }
            }
        }

        //Nur wenn der Vergleich nicht schon in der Gruppe schief geht, wird der DBUnique Vergleich gemacht.
        //Das hat den Vorteil, dass Eindeutigkeitsprüfungen über die gesammte Datenbank nur in Ausnahmefällen
        //kommuniziert werden müssen.
        return self::validateDBUnique($fieldList, $dlgValues, $brickDatabase, $dialogParams);
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $dlgValues
     * @param $brickDatabase
     * @param $viewType
     * @return null
     */
    private static function validateDBUnique($fieldList, $dlgValues, $brickDatabase, $dialogParams)
    {
        if (($fieldList) && ($dlgValues)) {
            foreach ($fieldList as $field) {
                $fieldName = $field->getFieldName();
                if ($field->isDbUnique() && $field->getDbUniqueResult()) {
                    $dlgValue = $dlgValues[$fieldName];
                    $groupId = $dlgValues['c4g_group_id'];
                    $groupKeyField = $dialogParams->getViewParams()->getGroupKeyField();

                    $t = $brickDatabase->getParams()->getTableName();

                    $columns = "$t.$fieldName='".$dlgValue."'";
                    if ($field->getDbUniqueAdditionalCondition()) {
                        $columns = $columns.' AND '.$field->getDbUniqueAdditionalCondition();
                    }

                    $arrColumns = array($columns);
                    $arrValues = array();
                    $arrOptions = array();

                    $dbValues = $brickDatabase->findBy($arrColumns, $arrValues, $arrOptions);

                    if ($dbValues !== null) {
                        $dbValue = $dbValues[0];
                        if ($dbValue && (!$dbValue->deaktivated) && (!$groupKeyField ||
                                ($dbValue->$groupKeyField != $groupId))) {
                            return $field->getDbUniqueResult();
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $dlgValues
     * @return null
     */
    public static function validateFields($fieldList, $dlgValues)
    {
        if (($fieldList) && ($dlgValues)) {
            foreach ($fieldList as $field) {
                if ($field instanceof C4GEmailField) {
                    $fieldName = $field->getFieldName();
                    $dlgValue = $dlgValues[$fieldName];
                    if ($dlgValue && (trim($dlgValue) != '')) {
                        $mail = C4GUtils::emailIsValid($dlgValue);
                        if (!$mail) {
                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_email'];
                        }
                    }
                } elseif ($field instanceof C4GTelField) {
                    $fieldName = $field->getFieldName();
                    $dlgValue = $dlgValues[$fieldName];
                    if ($dlgValue && (trim($dlgValue) != '')) {
                        //$phone = C4GUtils::phoneIsValid($dlgValue);
                        if ($fieldName != 'fax' && !preg_match('/^[\+0-9\-\/\(\)\s]*$/', $dlgValue)) {
                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_phone'];
                        } elseif (!preg_match('/^[\+0-9\-\/\(\)\s]*$/', $dlgValue)) {
                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_fax'];
                        }
                    }
                } elseif ($field instanceof C4GPostalField) {
                    $fieldName = $field->getFieldName();
                    $dlgValue = $dlgValues[$fieldName];
                    if ($dlgValue && (trim($dlgValue) != '')) {
                        $postal = C4GUtils::postalIsValid($dlgValue);
                        if (!$postal) {
                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_postal'];
                        }
                    }
                } else if ($field instanceof C4GUrlField) {
                    $fieldName = $field->getFieldName();
                    $dlgValue = $dlgValues[$fieldName];
                    if ($dlgValue && (trim($dlgValue) != '')) {
                        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $dlgValue)) {
                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_website'];
                        }
                    }
                } else if ($field instanceof C4GDecimalField || $field instanceof C4GNumberField) {
                    $fieldName = $field->getFieldName();
                    $dlgValue = $dlgValues[$fieldName];
                    if ($dlgValue && (trim($dlgValue) != '')) {
                        if (!preg_match("/(^[0-9,.]+$)/", $dlgValue)) {
                            return $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['validate_float'].$fieldName;
                        }
                    }
                } else {
                    continue;
                }
            }
        }
        return null;
    }

    /** Beginn DATENBANK METHODEN  **/
    /**
     * @param C4GBrickField[] $fieldList
     * @param $dlgValues
     * @param $dbValues
     * @param $viewType
     * @param $is_frozen
     * @return array
     */
    public static function compareWithDB($fieldList, $dlgValues, $dbValues, $viewType, $is_frozen)
    {
        $result = array();

        if (C4GBrickView::isWithoutEditing($viewType) || $is_frozen) {
            return $result;
        }

        if (($dbValues) && ($dlgValues)) {
            foreach ($fieldList as $field) {
                $fieldName = $field->getFieldName();

                if (!$field->isComparable())
                    continue;

                if (($field->isDatabaseField()) && (!$field->isFormField())) {
                    if ($field->getInitialValue()) {
                       $dlgValues[$fieldName] = $field->getInitialValue();
                    }
                }

                if ($field->isDatabaseField()) {
                    $compareResult = $field->compareWithDB($dbValues, $dlgValues);
                    if ($compareResult) {
                        if (is_array($compareResult) && sizeof($compareResult) > 0) {
                            // TODO prüfen ob dies den gewünschten effekt hat oder lieber die loop oben genommen wird
                            // TODO möglw. könnte man auch array_merge nutzen, aber nur wenn keine alphanumeric keys da sind
                            $result += $compareResult;
                        } else {
                            $result[] = $compareResult;
                        }
                    }
                }
            }
        }
        else {
            //Sonderlocke bei Neuanlage (keine dbvalues)
            if ($dlgValues) {
                foreach ($fieldList as $field) {
                    if (!$field->isComparable())
                        continue;

                    $fieldName = $field->getFieldName();
                    if (/*($viewType == C4GBrickViewType::MEMBERBOOKING) || */(($field->isFormField()) && ($field->isDatabaseField()))) {
                        //Muss davon ausgehen, dass boolean-Werte immer mit false vorbelegt sind. Funktioniert auch nur dann.
                        if (/*($viewType == C4GBrickViewType::MEMBERBOOKING) || */(($dlgValues[$fieldName] != null) && (trim($dlgValues[$fieldName]) != '') && ($dlgValues[$fieldName] != 'id') && ($dlgValues[$fieldName] != "false" ) && (intval($dlgValues[$fieldName]) !== $field->getInitialValue()) && ($dlgValues[$fieldName] != -1))) {
                            $result[] = new C4GBrickFieldCompare($field, '', $dlgValues[$fieldName]);
                        }
                    }
                }
            }
        }

        return $result;
    }

   /* public static function field2db($field) {
        return;
    }*/

    /**
     * @param $elementId
     * @param $tableName
     * @param C4GBrickField[] $fieldList
     * @param $dlgValues
     * @param $database
     * @param $dbValues
     * @param $viewType
     * @param $user_id
     * @return bool
     */
    public static function saveC4GDialog($elementId, $tableName, $fieldList, $dlgValues, $brickDatabase,  $dbValues, C4GBrickDialogParams $dialogParams, $user_id)
    {
        $viewType = $dialogParams->getViewType();
        if (C4GBrickView::isPublicBased($viewType) && ($viewType != C4GBrickViewType::PUBLICFORM)) {
            return false;
        }

        $set = array();

        if ($viewType == C4GBrickViewType::GROUPPROJECT) {
            $uuid = $dlgValues['c4g_project_uuid'];
            if ($uuid) {
                $set['uuid'] = $uuid;
            }
        } else {
            if ($dbValues->uuid == '') {
                $set['uuid'] = C4GBrickCommon::getGUID();
            }
        }

        if ($fieldList) {

            $id = null;
            $id_fieldName = null;
            foreach ($fieldList as $field) {
                if (!$field->isDatabaseField())
                    continue;
                $fieldName = $field->getFieldName();
                $fieldData = $dlgValues[$fieldName];
                if ( (!$field->isFormField()) && (!$fieldData) && ($field->getInitialValue()) ) {
                    $fieldData = $field->getInitialValue();
                } else if (($field instanceof C4GGeopickerField) || ($field instanceof C4GDateTimeLocationField)) {
                    $loc_geox = $dlgValues['geox'];
                    if (!$loc_geox) {
                        $loc_geox = '';
                    }
                    $loc_geoy = $dlgValues['geoy'];
                    if (!$loc_geoy) {
                        $loc_geoy = '';
                    }
                    $set['loc_geox'] = $loc_geox;
                    $set['loc_geoy'] = $loc_geoy;

                    $fieldData = null;
                } else if ($field instanceof C4GKeyField) {
                    $fieldData = $field->validateFieldValue($fieldData);
                    if ($fieldData == 0) {
                        $fieldData = null;
                    }
                    $id = $fieldData;
                    $id_fieldName = $fieldName;
                } else if ($field instanceof C4GFileField){
                    $fieldData = $field->createFieldData($dlgValues, $dbValues);
                } else {
                    $fieldData = $field->createFieldData($dlgValues);
                }

                if ($fieldData !== NULL) {
                    $set[$fieldName] = $fieldData;
                    if (!($field instanceof C4GFileField)) {
                        $set[$fieldName] = html_entity_decode(\c4g\C4GUtils::secure_ugc($fieldData));
                    }
                }
            }

            $memberKeyField = $dialogParams->getViewParams()->getMemberKeyField();
            $groupKeyField = $dialogParams->getViewParams()->getGroupKeyField();

            if (C4GBrickView::isWithMember($viewType) || C4GBrickView::isPublicBased($viewType)) {
                $memberId = $user_id;//$dlgValues['c4g_member_id'];
                if ( ($memberId) && ($memberId > 0) ) {
                    $set[$memberKeyField] = $memberId;
                }
            }

            if (C4GBrickView::isWithGroup($viewType)) {
                $groupId = $dlgValues['c4g_group_id'];
                if ( ($groupId) && ($groupId > 0) ) {
                    $set[$groupKeyField] = $groupId;
                }
            }

            if (C4GBrickView::isWithProject($viewType)) {
                $projectId = $dlgValues['c4g_project_id'];
                if ( ($projectId) && ($projectId > 0) ) {
                    $set['project_id'] = $projectId;
                }
            }

            if (C4GBrickView::isWithParent($viewType)) {
                $parentId = $dlgValues['c4g_parent_id'];
                if ( ($parentId) && ($parentId > 0) ) {
                    $set['pid'] = $parentId;
                }
            }

            //ToDo Sonderlocke
            if ($viewType == C4GBrickViewType::MEMBERBOOKING) {
                $memberId = $dlgValues['c4g_member_id'];
                if (($memberId) && ($memberId > 0)) {
                    $set[$memberKeyField] = $memberId;
                }

                $group_type = null;
                $group_type_id = 3;//$dlgValues['group_type_id'];
                if ($group_type_id) {
                    $group_type = C4gBookingGroupTypesModel::findById($group_type_id);
                }

                $groupId = $dlgValues['group_id'];
                $applicationgroup = null;
                if (!$groupId || $groupId <= 0) {
                    $group = new \c4g\MemberGroupModel();
                    $modules = \ModuleModel::findBy('type', 'c4g_groups');
                    if ($modules) {
                        $module = $modules[0];
                        if ($module) {
                            $group->cg_member_rights = $module->c4g_groups_default_member_rights;
                            $group->cg_owner_rights = $module->c4g_groups_default_owner_rights;

                            $applicationgroup = $module->c4g_groups_permission_applicationgroup;
                        }
                    }

                    $members = array();
                    $members[] = $user_id;
                    $group->cg_member = serialize($members);
                } else {
                    $group = \c4g\MemberGroupModel::findByPk($groupId);
                }

                if ($group_type) {
                    $group->name = \c4g\C4GUtils::secure_ugc($dlgValues['caption']);
                    $date = new \DateTime();
                    $group->tstamp = $date->getTimestamp();
                    $group->cg_max_member = $group_type->max_member_count;

                    if (($dlgValues['group_owner_id']) && ($dlgValues['group_owner_id'] > 0)) {
                        $owner_member_id = $dlgValues['group_owner_id'];
                        $set['group_owner_id'] = $owner_member_id;
                    } else {
                        $owner_member_id = $dialogParams->getMemberId();
                        $set['group_owner_id'] = $owner_member_id;
                    }

                    $group->cg_owner_id = $owner_member_id;

                    $group->save();

                    if (!$groupId) {
                        $set[$groupKeyField] = $group->id;
                        $owner = \c4g\MemberModel::findByPk($owner_member_id);
                        if ($owner && !empty($owner->groups)) {
                            $ownerGroups = unserialize($owner->groups);
                            $ownerGroups[] = $group->id;

                            $found = false;
                            if ($applicationgroup) {
                                foreach ($ownerGroups as $ownerGroup) {
                                    if ($ownerGroup == $applicationgroup) {
                                        $found = true;
                                    }
                                }

                                if (!$found) {
                                    $ownerGroups[] = $applicationgroup;
                                }
                            }
                            $owner->groups = serialize($ownerGroups);
                            $owner->save();
                        }
                    }
                }
            }

            if (($id == null) && ($elementId >= 0)) {
                $id = $elementId;
                $set[$id_fieldName] = $id;
            }

            $set['tstamp'] = time();

            $last_user = $set['last_member_id'];
            if ($last_user !== null) {
                $set['last_member_id'] = $user_id;
            }
            if ($dialogParams->getBeforeSaveAction()) {
                $action = $dialogParams->getBeforeSaveAction();
                $set = $action->call($set);
            }

            $result = false;
            if ($set[$id_fieldName] == null) {
                $result = $brickDatabase->insert($set);
            } else
            if (($id) && ($id_fieldName)) {
                $result = $brickDatabase->update($id, $set, $id_fieldName);
            }

            return $result;
        }
    }

    /**
     * @param $elementId
     * @param $tableName
     * @param $database
     * @param C4GBrickField[] $fieldList
     * @param $dbValues
     * @param $dlgValues
     * @param $userId
     * @return bool
     */
    public static function deleteC4GTableDataById($elementId, $tableName, $database, $fieldList,  $dbValues, $dlgValues, $userId){
        if ($elementId >= 0) {
            $files = array();
            if ($fieldList) {
                $deleteFlag = null;
                $publishedFlag = null;
                foreach ($fieldList as $field) {
                    $fieldName = $field->getFieldName();
                    if ($field instanceof C4GFileField) {
                        $files[] = $dbValues->$fieldName;
                    }
                    if ($field->getType() == C4GBrickFieldType::FLAG_DELETE) {
                        $deleteFlag = $field->getFieldName();
                    }
                    if ($field->getType() == C4GBrickFieldType::FLAG_PUBLISHED) {
                        $publishedFlag = $field->getFieldName();
                    }
                }
            }

            if ( (!$deleteFlag) && (!$publishedFlag)) {
                $objDeleteStmt = $database->prepare("DELETE FROM $tableName WHERE id = ?")
                    ->execute($elementId);

                if ($objDeleteStmt->affectedRows==0) {
                    return false;
                }


                foreach($files as $file) {
                    if (\Validator::isUuid($file)) {
                        C4GBrickCommon::deleteFileByUUID($file);
                    }
                }

                return true;
            } else {
                if ($deleteFlag) {
                    $dlgValues[$deleteFlag] = true;
                }

                if ($publishedFlag) {
                    $dlgValues[$publishedFlag] = false;
                }

                $result = C4GBrickDialog::saveC4GDialog($elementId, $tableName, $fieldList, $dlgValues, $database,  $dbValues, false, $userId);
                if ($result != false)
                    return true;
            }

        }

        return false;
    }

    /**
     * some fields combined in dialog, but for compare or saving we have to merge the fields in one list.
     * @param $fieldList
     * @return array
     */
    public static function makeRegularFieldList($fieldList) {
        $resultList = array();
        foreach ($fieldList as $field) {
            if ($field->getExtTitleField()) {
                $resultList[] = $field->getExtTitleField();
            }
            if ($field instanceof C4GGridField) {
                $grid = $field->getGrid();
                if ($grid) {
                    foreach ($grid->getElements() as $element) {
                        $elementField = $element->getField();
                        if ($elementField) {
                            $resultList[] = $elementField;
                        }
                    }
                }
            }

            $resultList[] = $field;
        }

        return $resultList;
    }

}