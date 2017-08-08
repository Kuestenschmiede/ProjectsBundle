<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 04.08.17
 * Time: 14:02
 */

namespace con4gis\ProjectBundle\Classes\Actions;


class C4GChangeFieldAction extends C4GBrickAction
{

    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $fieldList = $this->getFieldList();
        $listParams = $this->getListParams();
        $putVars = $this->getPutVars();
        $brickDatabase = $this->getBrickDatabase();
        // check for each field if there is an entry in the post array
        // TODO check if we can use this in PHP 5
        /* @var C4GBrickField $field */
        foreach($fieldList as $field) {
            if ($_POST['c4g_' . $field->getFieldName()]) {
                $changes = $_POST['c4g_' . $field->getFieldName()];
                foreach($changes as $property=>$value) {
                    // TODO check with longer property names, in cases like setFieldName
                    $setter = 'set' . ucfirst($property);
                    $value = $this->checkVal($value);
                    $field->$setter($value);
                }
            }
        }
        return $fieldList;
    }

    private function checkVal($value) {
        if ($value === "true" || $value === "false") {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        } else {
            return $value;
        }
    }
}