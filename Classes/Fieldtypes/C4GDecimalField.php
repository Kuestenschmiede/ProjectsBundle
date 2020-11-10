<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickRegEx;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldNumeric;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GDecimalField extends C4GBrickFieldNumeric
{
    protected $decimals = 2;
    protected $allowNegative = false;

    public function __construct()
    {
        $this->setAlign('right');
    }

    //ToDo onChange Aufruf zur Darstellung ausgebaut. Diesen müssen wir wieder nachrüsten
    //-> C4GBrickDialog.js - changeNumberFormat

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $this->setPattern(C4GBrickRegEx::generateNumericRegEx($this->getDecimals(), $this->allowNegative, $this->getThousandsSep(), $this->getDecimalPoint()));
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        if ($this->getThousandsSep() !== '') {
            $value = number_format(str_replace(',', '.', $this->generateInitialValue($data)), $this->getDecimals(), $this->getDecimalPoint(), $this->getThousandsSep());
        } else {
            $value = $this->generateInitialValue($data);
        }
        if ($this->getDecimalPoint() === ',' && strpos($value, ',') === false) {
            $value = str_replace('.', ',', $value);
        }
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                    '<input pattern="' . $this->pattern . '" ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" step="any" id="' . $id . '" class="formdata ' . $id . '" size="' . $this->getSize() . '" min="' . $this->getMin() . '" max="' . $this->getMax() . '" name="' . $this->getFieldName() . '" value="' . $value . '" >');
        }

        return $result;
    }

    public function getC4GListField($rowData, $content)
    {
        $fieldName = $this->getFieldName();
        $value = $rowData->$fieldName;
        /*
        if($this->getThousandsSep() !== '') {
            $value = number_format(str_replace(',','.',$value), $decimals, $this->getDecimalPoint(), $this->getThousandsSep());
        } else {
            $value = number_format($value, $this->getListDecimals(), $decimals, '');
        }
        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue().$value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value.$this->getAddStrBehindValue();
        }*/

        return $value;
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
        if ($point !== null && $comma !== null) {
            if ($point > $comma) {
                # 1,000.99
                $value = str_replace(',', '', $value);
            } else {
                # 1.000,99
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            }
        } elseif ($point === null && $comma !== null) {
            $value = str_replace(',', '.', $value);
        }
        settype($value, 'float');
        // $value = round($value, 2);
        //ToDo zur Berechnung, Speicherung, etc. muss die Umwandlung auch noch anderer Stelle gemacht/geprüft werden.
        //Hier setzen wir für die Berechnung bis dahin Standardwerte.
        if ($value && ($point || $comma)) {
            $value = number_format($value, $this->getDecimals(), $this->getDecimalPoint(), '');
        }

        return $value;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValues
     * @param $dlgValues
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
        if ($this->isSearchField()) {
            if (!$dbValues->$fieldname) {
                $pos = strripos($fieldname, '_');
                if ($pos !== false) {
                    $fieldName = substr($fieldname, 0, $pos);
                }
                if ($this->isSearchMinimumField()) {
                    $dbValue = $dbValues->$fieldName;
                    $maximum_fieldname = $fieldName . '_maximum';
                    if ($dbValue !== 0 && !($dbValue > $dlgValue && $dbValue < $dlgValues[$maximum_fieldname])) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
                } elseif ($this->isSearchMaximumField() && $dbValue !== 0) {
                    if ($dbValue > $dlgValue) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
                }
            }
        } elseif (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getThousandsSep()
    {
        return $this->thousands_sep;
    }

    /**
     * @param $thousands_sep
     * @return $this|C4GBrickFieldNumeric
     */
    public function setThousandsSep($thousands_sep)
    {
        $this->thousands_sep = $thousands_sep;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecimalPoint()
    {
        return $this->decimal_point;
    }

    /**
     * @param $decimal_point
     * @return $this|C4GBrickFieldNumeric
     */
    public function setDecimalPoint($decimal_point)
    {
        $this->decimal_point = $decimal_point;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param $decimals
     * @return $this|C4GBrickFieldNumeric
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowNegative()
    {
        return $this->allowNegative;
    }

    /**
     * @param $allowNegative
     * @return $this
     */
    public function setAllowNegative($allowNegative)
    {
        $this->allowNegative = $allowNegative;

        return $this;
    }

    public function getRegEx()
    {
        return C4GBrickRegEx::generateNumericRegEx($this->getDecimals(), $this->allowNegative, $this->getThousandsSep(), $this->getDecimalPoint());
    }
}
