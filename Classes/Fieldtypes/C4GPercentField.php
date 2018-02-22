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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldNumeric;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickRegEx;


class C4GPercentField extends C4GBrickFieldNumeric
{
    /**
     * @property string $group Group of PercentFields this belongs to. All percent fields with a non-empty group must
     * have a combined value smaller or equal to 100. //Todo does not work yet
     */
    protected $percentGroup = '';

    /**
     * C4GPercentField constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setMax(100);
    }


    /**
     * @param $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        if ($this->getDecimals() <= 0) {

            $required = $this->generateRequiredString($data, $dialogParams);

            $result = '';
            $id = "c4g_" . $this->getFieldName();
            $onChange = '';

            if ($this->getThousandsSep() !== '') {
                $number = str_replace(',', '.', $this->generateInitialValue($data));
                if ($number) {
                    $value = number_format($number, 0, '', $this->getThousandsSep());
                } else {
                    $value = $this->generateInitialValue($data);
                }
                $onChange = 'onChange="changeNumberFormat(\'' . $id . '\',this.value)"';
                $type = 'text';
            } else {
                $value = $this->generateInitialValue($data);
                $type = 'number';
            }
            if ($this->isShowIfEmpty() || !empty($value)) {

                $condition = $this->createConditionData($fieldList, $data);

                $result =
                    $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                        '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="' . $type . '" ' . $onChange . ' id="' . $id . '" class="formdata ' . $id . '" size="' .
                        $this->getSize() . '" min="' . $this->getMin() . '" max="' . $this->getMax() . '" step="' . $this->getStep() . '" pattern="'. C4GBrickRegEx::NUMBERS_NO_SEP .'" name="' .
                        $this->getFieldName() . '" value="' . $value . '">');
            }

            return $result;
        } else {
            $id = "c4g_" . $this->getFieldName();
            $required = $this->generateRequiredString($data, $dialogParams);
            if($this->getThousandsSep() !== '') {
                $value = number_format(str_replace(',','.',$this->generateInitialValue($data)), $this->getDecimals(), $this->getDecimalPoint(), $this->getThousandsSep());
            } else {
                $value = $this->generateInitialValue($data);
            }
            $result = '';

            if ($this->isShowIfEmpty() || !empty($value)) {

                $condition = $this->createConditionData($fieldList, $data);

                $result =
                    $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                        '<input pattern="' . C4GBrickRegEx::generateNumericRegEx($this->getDecimals(), false, $this->getThousandsSep(), $this->getDecimalPoint()) . '" ' . $required . ' ' . $condition['conditionPrepare'] . ' type="number" step="any" id="' . $id . '" class="formdata ' . $id . '" size="' . $this->getSize() . '" min="' . $this->getMin() . '" max="' . $this->getMax() . '" name="' . $this->getFieldName() . '" value="' . $value . '" >');
            }
            return $result;
        }
    }

    /**
     * Check and transform decimal point if necessary.
     * @param string $value
     * @return float $value
     * @author nka
     */
    public function validateFieldValue($value)
    {
        $value = str_replace(' ', '', $value);
        $point = strpos($value, '.');
        $comma = strpos($value, ',');
        if ($point !== NULL && $comma !== NULL) {
            if ($point > $comma) {
                # 1,000.99
                $value = str_replace(',', '', $value);
            } else {
                # 1.000,99
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            }
        } else if ($point === NULL && $comma !== NULL) {
            $value = str_replace(',', '.', $value);
        }
        settype($value, "float");
        // $value = round($value, 2);
        //ToDo zur Berechnung, Speicherung, etc. muss die Umwandlung auch noch anderer Stelle gemacht/geprüft werden.
        //Hier setzen wir für die Berechnung bis dahin Standardwerte.
        if ($value && ($point || $comma)) {
            $value = number_format($value,$this->getDecimals(),'.','');
        }
        return $value;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        if($this->getThousandsSep() !== ''){
            $dlgValue = str_replace('.','',$dlgValue);
        }
        $result = null;

        if($this->isSearchField()) {
            if (!$dbValues->$fieldname) {
                $pos = strripos($fieldname, '_');
                if ($pos !== false) {
                    $fieldName = substr($fieldname, 0, $pos);
                }
                if ($this->isSearchMinimumField()) {
                    $dbValue = $dbValues->$fieldName;
                    $maximum_fieldname =$fieldName.'_maximum';
                    if ($dbValue !== 0 && !($dbValue > $dlgValue && $dbValue < $dlgValues[$maximum_fieldname])) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
                } else if ($this->isSearchMaximumField() && $dbValue !== 0) {
                    if ($dbValue > $dlgValue) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
                }
            }
        }
         elseif (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
         }
        return $result;
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues) {
        $value = str_replace($this->getThousandsSep(),'',$dlgValues[$this->getFieldName()]);
        $value = floatval($value);
        $dlgValues[$this->getFieldName()] = $value;
        return $dlgValues[$this->getFieldName()];
    }

    /**
     * @return mixed
     */
    public function getPercentGroup()
    {
        return $this->percentGroup;
    }

    /**
     * @param mixed $percentGroup
     */
    public function setPercentGroup($percentGroup)
    {
        $this->percentGroup = $percentGroup;
    }


}