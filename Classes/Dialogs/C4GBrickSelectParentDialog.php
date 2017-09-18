<?php
/**
 * Created by PhpStorm.
 * User: mei
 * Date: 08.03.17
 * Time: 08:30
 */
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use Contao\System;
use Eden\CustomerBundle\classes\contao\modules\EdenCustomerAddresses;

class C4GBrickSelectParentDialog extends C4GBrickDialog
{
    private $brickDatabase = null;
    private $module = null;

    /**
     * C4GBrickSelectParentDialog constructor.
     * @param $dialogParams
     * @param $brickDatabase
     * @param null $module
     */
    public function __construct($dialogParams, $brickDatabase, $module = null)
    {
        parent::__construct($dialogParams);
        $this->brickDatabase = $brickDatabase;
        $this->module = $module;
    }


    /**
     * @param $memberId
     * @param $group_id
     * @return array
     */
    public function show()
    {
        $dialogParams = $this->getDialogParams();
        $group_id = $dialogParams->getGroupId();
        $parent_id = $dialogParams->getParentId();
        $project_id = $dialogParams->getProjectId();
        $parentModel = $dialogParams->getParentModel();
        $parentCaption = $dialogParams->getParentCaption();
        $parentCaptionPlural = $dialogParams->getParentCaptionPlural();
        $parentCaptionFields = $dialogParams->getParentCaptionFields();
        $parentCaptionCallback = $dialogParams->getParentCaptionCallback();
        $confirmAction = C4GBrickActionType::ACTION_CONFIRMPARENTSELECT;
        $confirmButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_CONFIRM_BUTTON'];

        $cancelAction = C4GBrickActionType::ACTION_CANCELPARENTSELECT;
        $cancelButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_CANCEL_BUTTON'];

        $parentlist = array();

        $groupKeyField = $dialogParams->getViewParams()->getGroupKeyField();
        if ($project_id && $project_id > 0) {
            $items = $parentModel::findby('project_id', $project_id);
        } else {
            $items = $parentModel::findby($groupKeyField, $group_id);
        }

        if (!$items) {
            return array(
                'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_ERROR'].$parentCaption.'.'
            );
        }
        if ($parentCaptionCallback && is_array($parentCaptionCallback)) {
            // call module function for all items
            // [id1 => caption1, id2 => caption2, ...]
            $class = $parentCaptionCallback[0];
            $function = $parentCaptionCallback[1];
            $arrCaptions = $class::$function(
                $items,
                $this->brickDatabase->getEntityManager()
            );
        }

        foreach($items as $item) {
            // default case
            $caption = $item->caption;
            if (!$caption) {
                $caption = $item->name;
            }
            // simple dynamic caption
            if ($parentCaptionFields && is_array($parentCaptionFields)) {
                $caption = '';
                foreach($parentCaptionFields as $key=>$value) {
                    if (strlen($value) == 1) {
                        if ($value == ')') {
                            //if there is no bracketed value remove brackets
                            if (substr(trim($caption), -1, 1) == '(') {
                                $caption = substr(trim($caption), 0, -1);
                            } else {
                                $caption = trim($caption).$value;
                            }
                        } else {
                            $caption .= $value;
                        }
                    } else {
                        $caption .= $item->$value . ' ';
                    }
                }
            }
            // caption via callback
            if ($parentCaptionCallback && is_array($parentCaptionCallback)) {
                $caption = $arrCaptions[$item->id];
            }
            $id = $item->id;
            $parentlist[] = array(
                'id'     => $id,
                'name'   => $caption);
        }

        $parentField = new C4GSelectField();
        $parentField->setFieldName('parent_id');
        $parentField->setTitle(sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT'], $parentCaptionPlural));
//        $parentField->setType(C4GBrickFieldType::SELECT);
        $parentField->setSortColumn(false);
        $parentField->setTableColumn(false);
        if ($dialogParams->isWithEmptyParentOption()) {
            $parentField->setWithEmptyOption(true);
            $parentField->setEmptyOptionLabel('Alle ' . $parentCaptionPlural);
        }
        $parentField->setSize(1);
        $parentField->setOptions($parentlist);
        $parentField->setChosen(true);
//        $parentField->setMinChosenCount(0); //Test

        if ( $parent_id && ($parent_id > -1)) {
            $parentField->setInitialValue($parent_id);
        }

        return C4GBrickDialog::showC4GSelectDialog($dialogParams, $parentField,
            sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_CONFIRM_QUESTION'],$parentCaptionPlural),
            $confirmAction, $confirmButtonText, $cancelAction, $cancelButtonText);
    }

}