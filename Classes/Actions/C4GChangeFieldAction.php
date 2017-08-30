<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 04.08.17
 * Time: 14:02
 */

namespace con4gis\ProjectBundle\Classes\Actions;


use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickField;

class C4GChangeFieldAction extends C4GBrickAction
{
    private $module = null;

    /**
     * C4GChangeFieldAction constructor.
     * @param $dialogParams
     * @param $listParams
     * @param $fieldList
     * @param $putVars
     * @param $brickDatabase
     * @param $module
     */
    public function __construct($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase, $module)
    {
        parent::__construct($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
        $this->module = $module;
    }

    public function run()
    {
        $fieldList = $this->getFieldList();
        $changeHandler = $this->module->getDialogChangeHandler();
        $changes = $this->getChangesFromPost($fieldList);
        $fieldList = $changeHandler->applyChanges($changes, $fieldList, $this->module->getBrickKey(), true);
        return $fieldList;
    }

    /**
     * Checks the global $_POST array if there are any entries for fields from the fieldlist.
     * If yes, they are stored in the returned array, with the fieldName as key.
     * @param $fieldList
     * @return array
     */
    private function getChangesFromPost($fieldList)
    {
        $changes = array();
        /* @var C4GBrickField $field */
        foreach ($fieldList as $field) {
            if ($_POST['c4g_' . $field->getFieldName()]) {
                $changes[$field->getFieldName()] = $_POST['c4g_' . $field->getFieldName()];
            }
        }
        return $changes;
    }
}