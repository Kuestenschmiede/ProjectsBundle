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
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Actions\C4GShowRedirectDialogAction;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GSelectField;
use Contao\System;
use Eden\CustomerBundle\classes\contao\modules\EdenCustomerAddresses;

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

        $parentList = array();

        $parentData = $parentModel::findParents();

        if (is_array($parentData)) {
            foreach ($parentData as $key => $parent) {
                $parentList[$key]['id'] = $parent['id'];
                $parentList[$key]['name'] = $parent['name'];
            }
        } elseif ($parentData instanceof \stdClass) {
            foreach ($parentData as $key => $parent) {
                $parentList[$key]['id'] = $parent->id;
                $parentList[$key]['name'] = $parent->name;
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

        if ( $parent_id && ($parent_id > 0)) {
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