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
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Actions\C4GShowRedirectDialogAction;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;

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

        $parentlist = [];

        $groupKeyField = $dialogParams->getViewParams()->getGroupKeyField();
        if ($project_id && $project_id > 0) {
            $items = $parentModel::findby('project_id', $project_id);
        } else {
            $items = $parentModel::findby($groupKeyField, $group_id);
        }

        if (!$items) {
            $redirects = $dialogParams->getRedirects();
            if ($redirects) {
                foreach ($redirects as $redirect) {
                    $redirect->setActive($redirect->getType() == C4GBrickConst::REDIRECT_PARENT);
                }

                $action = new C4GShowRedirectDialogAction(
                    $dialogParams,
                    $this->listParams,
                    $this->fieldList,
                    $this->putVars,
                    $this->brickDatabase
                );

                return $action->run();
            }

            return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_ERROR'] . $parentCaption . '.'];
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

        foreach ($items as $item) {
            // default case
            $caption = $item->caption;
            if (!$caption) {
                $caption = $item->name;
            }
            // simple dynamic caption
            if ($parentCaptionFields && is_array($parentCaptionFields)) {
                $caption = '';
                foreach ($parentCaptionFields as $key => $value) {
                    if (strlen($value) == 1) {
                        if ($value == ')') {
                            //if there is no bracketed value remove brackets
                            if (substr(trim($caption), -1, 1) == '(') {
                                $caption = substr(trim($caption), 0, -1);
                            } else {
                                $caption = trim($caption) . $value;
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
            $parentlist[] = [
                'id' => $id,
                'name' => $caption, ];
        }

        $parentField = new C4GSelectField();
        $parentField->setFieldName('parent_id');
        if ($parentCaptionPlural) {
            $parentField->setTitle(sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT'], $parentCaptionPlural));
        } else {
            $parentField->setTitle(sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT'], $parentCaption));
        }
//        $parentField->setTitle(sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT'], $parentCaptionPlural));
//        $parentField->setType(C4GBrickFieldType::SELECT);
        $parentField->setSortColumn(false);
        $parentField->setTableColumn(false);
        if ($dialogParams->isWithCommonParentOption()) {
            $parentField->setWithEmptyOption(true);
            $parentField->setEmptyOptionLabel('Alle ' . $parentCaptionPlural); //ToDO Language
        }
        $parentField->setSize(1);
        $parentField->setOptions($parentlist);
        $parentField->setChosen(true);
//        $parentField->setMinChosenCount(0); //Test

        if ($parent_id && ($parent_id > -1)) {
            $parentField->setInitialValue($parent_id);
        }

        return C4GBrickDialog::showC4GSelectDialog($dialogParams, $parentField,
            sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_CONFIRM_QUESTION'],
                $parentCaptionPlural ? $parentCaptionPlural : $parentCaption),
            $confirmAction, $confirmButtonText, $cancelAction, $cancelButtonText);
    }
}
