<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 04.08.17
 * Time: 14:02
 */

namespace con4gis\ProjectsBundle\Classes\Actions;


use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GChangeFieldAction extends C4GBrickAction
{

    public function __construct($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase, $module = null)
    {
        parent::__construct($dialogParams, $listParams, $fieldList, $putVars, $brickDatabase);
        if (isset($module)) {
            $this->setmodule($module);
        }
    }


    public function run()
    {
        $fieldList = $this->getFieldList();
        $changeHandler = $this->module->getDialogChangeHandler();
        $changes = $this->getChangesFromPost($fieldList);
        $fieldList = $changeHandler->applyChanges($changes, $fieldList, $this->module->getBrickKey(), true);
        $this->getModule()->setFieldList($fieldList);
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