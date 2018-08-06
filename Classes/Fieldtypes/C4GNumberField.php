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
class C4GNumberField extends C4GBrickFieldNumeric
{
    protected $pattern = C4GBrickRegEx::DIGITS_NEG;
    protected $initialValue = 0;

    public function __construct()
    {
        $this->setAlign("right");
    }

    protected $thousands_sep = '';
    /**
     * @param $fieldList
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $required = $this->generateRequiredString($data, $dialogParams);

        $result = '';
        $id = "c4g_" . $this->getFieldName() . '_' . $this->getAdditionalId();
        $onChange = '';

        if($this->getThousandsSep() !== '') {
            $number = str_replace(',','.',$this->generateInitialValue($data));
            if ($number) {
                $value = number_format($number, 0, ',', $this->getThousandsSep());
            } else {
                $value = $this->generateInitialValue($data);
            }
//            $onChange = 'onChange="changeNumberFormat(\'' . $id . '\',this.value)"'; This neither seems to work properly nor does it seem needed - rro
            $type = 'text';
        } else {
            $value = $this->generateInitialValue($data);
            $type = 'number';
        }
        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                    '<input ' . $required . ' ' . $condition['conditionPrepare'] .  ' type="'.$type. '" ' .$onChange . ' id="' . $id . '" class="formdata ' . $id . '" size="' .
                    $this->getSize() . '" min="' . $this->getMin() . '" max="' . $this->getMax() . '" step="'.$this->getStep().'" pattern="'.$this->pattern.'" name="' .
                    $this->getFieldName() . '" value="' . $value . '">');
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

}