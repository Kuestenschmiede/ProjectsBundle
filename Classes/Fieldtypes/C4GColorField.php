<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Fieldtypes;

use con4gis\ProjectBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GColorField extends C4GBrickField
{
    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
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
            $conditionPrepare = array();

            if (!$conditionResult) {
                $conditionPrepare = array('style="display: none;"', 'disabled');
                $prepareCondition = array(
                    'conditionPrepare' => 'style="display: none;"');
            } else {
                $prepareCondition = array(
                    'conditionPrepare' => 'style="display: block;"');

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
                '<input ' . $required . ' type="color" id="' . $id . '" class="formdata ' . $id . '" name="' . $fieldName . '" value="' . $value . '" ' . $conditionPrepare[1] . '>' .
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