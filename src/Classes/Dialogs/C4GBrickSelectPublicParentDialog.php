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
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;

class C4GBrickSelectPublicParentDialog extends C4GBrickDialog
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
     * @return array|mixed
     */
    public function show()
    {
        $dialogParams = $this->getDialogParams();
        $parent_id = $dialogParams->getParentId();
        $parentModel = $dialogParams->getParentModel();
        $confirmAction = C4GBrickActionType::ACTION_CONFIRMPARENTSELECT;
        $confirmButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_CONFIRM_BUTTON'];

        $cancelAction = C4GBrickActionType::ACTION_CANCELPARENTSELECT;
        $cancelButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_PARENT_DIALOG_CANCEL_BUTTON'];

        $parentList = [];
        $parentList[0]['id'] = 0;
        $parentList[0]['name'] = ' - ';

        $parentData = $parentModel::findParents();

        if (is_array($parentData)) {
            foreach ($parentData as $key => $parent) {
                $parentList[intval($key) + 1]['id'] = $parent['id'];
                $parentList[intval($key) + 1]['name'] = $parent['name'];
            }
        } elseif ($parentData instanceof \stdClass) {
            foreach ($parentData as $key => $parent) {
                $parentList[intval($key) + 1]['id'] = $parent->id;
                $parentList[intval($key) + 1]['name'] = $parent->name;
            }
        }

        $parentField = new C4GSelectField();
        $parentField->setFieldName('parent_id');
        $parentField->setTitle($dialogParams->getSelectParentCaption());
        $parentField->setSortColumn(false);
        $parentField->setTableColumn(false);
        $parentField->setSize(1);
        $parentField->setOptions($parentList);
        $parentField->setChosen(true);

        if ($parent_id && ($parent_id > 0)) {
            $parentField->setInitialValue($parent_id);
        }

        return C4GBrickDialog::showC4GSelectDialog(
            $dialogParams,
            $parentField,
            $dialogParams->getSelectParentMessage(),
            $confirmAction,
            $confirmButtonText,
            $cancelAction,
            $cancelButtonText
        );
    }
}
