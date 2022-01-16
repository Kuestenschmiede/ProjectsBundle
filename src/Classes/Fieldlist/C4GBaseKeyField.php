<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldlist;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;

abstract class C4GBaseKeyField extends C4GBrickField
{
    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $result = '';
        $id = 'c4g_' . $this->getFieldName();
        if ($this->isHidden()) {
            $type = 'hidden';
        } else {
            $type = 'number';
        }
        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="' . $type . '" id="' . $id . '" class="formdata ' . $id . '" size="' .
                $this->getSize() . '" pattern="\d*" name="' .
                $this->getFieldName() . '" value="' . $value . '" >');
        }

        return $result;
    }

    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|\con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare|null
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues)
    {
        return intval($dlgValues[$this->getFieldName()]);
    }

    /**
     * Checks if the datatype of the key is correct (integer)
     * @param $value
     * @return mixed
     */
    public function validateFieldValue($value)
    {
        if (is_string($value)) {
            return intval($value);
        }

        return $value;
    }
}
