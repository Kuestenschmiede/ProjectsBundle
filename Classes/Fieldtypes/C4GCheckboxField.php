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
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickList;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickTiles;

class C4GCheckboxField extends C4GBrickField
{
    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $checked = '';

        if (C4GBrickCommon::strToBool($value)) {
            $checked = 'checked';
        }

        $elementId = null;
        $result = '';
        $boolswitch = '';
        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            foreach ($fieldList as $afield) {
                $fieldConditions = $afield->getCondition();

                foreach ($fieldConditions as $fieldCondition) {
                    if (($fieldCondition) && ($fieldCondition->getType() == C4GBrickConditionType::BOOLSWITCH) && ($fieldCondition->getFieldName() == $this->getFieldName())) {
                        $elementId = 'c4g_' . $afield->getFieldName();
                        $reverse = 0;
                        if (!$fieldCondition->getValue()) {
                            $reverse = 1;
                        }
                        $boolswitch = ' onchange="handleBoolSwitch(' . $id . ',' . $elementId . ',' . $reverse . ')" ';

                        break;
                    }
                }
            }

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                '<input ' . $required . $boolswitch . $condition['conditionPrepare'] . ' type="checkbox" id="' . $id . '" class="formdata ' . $id . '" name="' . $this->getFieldName() . '" value="' . $this->getFieldName() . '" ' . $checked . '>');
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
        $dbValue = C4GBrickCommon::strToBool($dbValue);
        $dlgValue = C4GBrickCommon::strToBool($dlgvalue);
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
        $fieldData = $dlgValues[$this->getFieldName()];
        if ($fieldData == 'true') {
            $fieldData = 1;
        } else {
            $fieldData = 0;
        }

        return $fieldData;
    }

    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField($rowData, $content)
    {
        $fieldName = $this->getFieldName();

        return C4GBrickList::translateBool($rowData->$fieldName);
    }

    /**
     * Public method for creating the field specific tile HTML
     * @param $fieldTitle
     * @param $element
     * @return mixed
     */
    public function getC4GTileField($fieldTitle, $element)
    {
        $fieldName = $this->getFieldName();

        return $fieldTitle . '<div class="c4g_tile value">' . C4GBrickTiles::translateBool($element->$fieldName) . '</div>';
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        return C4GBrickList::translateBool($value);
    }

    /**
     * Returns false if the field is not mandatory or if it is mandatory but its conditions are not met.
     * Otherwise it checks whether the field has a valid value and returns the result.
     * @param array $dlgValues
     * @return bool|C4GBrickField
     */
    public function checkMandatory($dlgValues)
    {
        //$this->setSpecialMandatoryMessage($this->getFieldName());
        if (!$this->isMandatory()) {
            return false;
        } elseif (!$this->isDisplay()) {
            return false;
        } elseif ($this->getCondition()) {
            foreach ($this->getCondition() as $con) {
                $fieldName = $con->getFieldName();
                if (!$con->checkAgainstCondition($dlgValues[$fieldName])) {
                    return false;
                }
            }
        }
        $fieldName = $this->getFieldName();
        $check = C4GBrickCommon::strToBool($dlgValues[$fieldName]);
        if (!$check) {
            return $this;
        }

        return false;
    }

    /**
     * @param $data
     * @param $groupId
     * @return string
     */
    public function getC4GPopupField($data, $groupId)
    {
        if ($data[$this->getFieldName()]) {
            $styleClass = $this->getStyleClass();
            $checked = 'checked';

            return '<p class=' . $styleClass . '><b>' . $this->getTitle() . '</b>: ' . '<input type="checkbox" name="' . $this->getFieldName() . '" value="' . $this->getFieldName() . '" ' . $checked . '>' . '</p>';
        }

        return '';
    }
}
