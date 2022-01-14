<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GColorField extends C4GBrickField
{
    private $type = C4GBrickFieldType::COLOR;

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $fieldName = $this->getFieldName();
        $id = $this->createFieldID();
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);

        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->getCondition();
            $conditionResult = $this->checkCondition($fieldList, $data, $condition);
            $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);
            $conditionPrepare = [];

            if (!$conditionResult) {
                $conditionPrepare = ['style="display: none;"', 'disabled'];
                $prepareCondition = [
                    'conditionPrepare' => 'style="display: none;"', ];
            } else {
                $prepareCondition = [
                    'conditionPrepare' => 'style="display: block;"', ];
            }

            $conditionname = '';
            $conditiontype = '';
            $conditionvalue = '';

            if (!empty($condition)) {
                $conditionname = $condition->getFieldName();
                $conditiontype = $condition->getType();
                $conditionvalue = $condition->getValue();
            }

            $result =
                $this->addC4GField($prepareCondition,$dialogParams,$fieldList,$data,
                '<input ' . $required . ' type="color" id="' . $id . '" class="formdata c4g__form-color ' . $id . '" name="' . $fieldName . '" value="' . $value . '" ' . $conditionPrepare[1] . '>' .
                $description);
        }

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = C4GBrickCommon::strToFloat($dbValue);
        $dlgValue = C4GBrickCommon::strToFloat($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
    }
}
