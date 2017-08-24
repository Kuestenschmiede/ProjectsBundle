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

class C4GDecimalField extends C4GBrickField
{
    private $thousands_sep = '';
    private $decimal_point = ',';
    private $decimals = 2;

    public function __construct()
    {
        $this->setAlign("right");
    }

    //ToDo onChange Aufruf zur Darstellung ausgebaut. Diesen müssen wir wieder nachrüsten
    //-> C4GBrickDialog.js - changeNumberFormat

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
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
                '<input pattern="(\d+)(\.*)(\d+)(,*)(\d+)[^a-zA-Z]" ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" step="any" id="' . $id . '" class="formdata ' . $id . '" size="' . $this->getSize() . '" min="' . $this->getMin() . '" max="' . $this->getMax() . '" name="' . $this->getFieldName() . '" value="' . $value . '" >');
        }
        return $result;
    }

    /**
     * Dezimal-Trennzeichen prüfen und ggf. umwandeln.
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
     * @param $dbValue
     * @param $dlgvalue
     * @return C4GBrickFieldCompare
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = C4GBrickCommon::strToFloat($dbValue);
        $dlgValue = C4GBrickCommon::strToFloat($dlgvalue);
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
        else if (strcmp($dbValue, $dlgValue) != 0) {
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
     * @return string
     */
    public function getThousandsSep()
    {
        return $this->thousands_sep;
    }

    /**
     * @param string $thousands_sep
     */
    public function setThousandsSep($thousands_sep)
    {
        $this->thousands_sep = $thousands_sep;
    }

    /**
     * @return string
     */
    public function getDecimalPoint()
    {
        return $this->decimal_point;
    }

    /**
     * @param string $decimal_point
     */
    public function setDecimalPoint($decimal_point)
    {
        $this->decimal_point = $decimal_point;
    }

    /**
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param int $decimals
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
    }

}