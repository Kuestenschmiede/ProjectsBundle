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
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldNumeric;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickRegEx;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GNumberField extends C4GBrickFieldNumeric
{
    protected $pattern = C4GBrickRegEx::DIGITS_NEG;
    protected $initialValue = 0;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::INT)
    {
        parent::__construct($type);
        $this->setAlign('right');
    }

    protected $thousands_sep = '';
    /**
     * @param $fieldList
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);

        $result = '';
        $fieldName = $this->getFieldName();
        $id = 'c4g_' . $fieldName;
        if ($this->getAdditionalID()) {
            $id .= '_' . $this->getAdditionalId();
            $fieldName .= '_' . $this->getAdditionalId();
        }
        //$onChange = '';

        if ($this->getThousandsSep() !== '') {
            $number = str_replace(',', '.', $this->generateInitialValue($data));
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

            $changeAction = '';

            if ($this->isCallOnChange()) {
                if ($this->getCallOnChangeFunction()) {
                    $changeAction = 'onchange="' . $this->getCallOnChangeFunction() . 'handleBrickConditions();"'; //ToDo check with both solutions
                } else {
                    $changeAction = 'onchange="handleBrickConditions();"';
                }
            }

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                    '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="' . $type . '" ' . $changeAction . ' id="' . $id . '" class="formdata c4g__form-control c4g__form-'.$type.'-input ' . $id . '" size="' .
                    $this->getSize() . '" min="' . $this->getMin() . '" max="' . $this->getMax() . '" step="' . $this->getStep() . '" pattern="' . $this->pattern . '" name="' .
                    $fieldName . '" value="' . $value . '">');
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
        $fieldName = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;

        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldName .= '_' . $additionalId;
        }

        $dlgvalue = $dlgValues[$fieldName];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        if ($this->getThousandsSep() !== '') {
            $dlgValue = str_replace('.', '', $dlgValue);
        }
        $result = null;

        if ($this->isSearchField()) {
//ToDo check unused code
//            if (!$dbValue) {
//                $pos = strripos($fieldname, '_');
//                if ($pos !== false) {
//                    $fieldName = substr($fieldname, 0, $pos);
//                }
                if ($this->isSearchMinimumField()) {
                    //$dbValue = $dbValues->$fieldName;
                    $maximum_fieldname = $fieldName . '_maximum';
                    if ($dbValue !== 0 && !($dbValue > $dlgValue && $dbValue < $dlgValues[$maximum_fieldname])) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
                } elseif ($this->isSearchMaximumField() && $dbValue !== 0) {
                    if ($dbValue > $dlgValue) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
//                }
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

    public function checkMandatory($dlgValues)
    {
        if (!$this->isMandatory()) {
            return false;
        } elseif (!$this->isDisplay()) {
            return false;
        } elseif ($this->getCondition()) {
            foreach ($this->getCondition() as $con) {
                if (empty($con)) {
                    continue;
                }
                $fieldName = $con->getFieldName();
                if (!$con->checkAgainstCondition($dlgValues[$fieldName])) {
                    return false;
                }
            }
        }
        $fieldName = $this->getFieldName();
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldName .= '_' . $additionalId;
        }

        $fieldData = $dlgValues[$fieldName];
        if (is_string($dlgValues[$fieldName])) {
            $fieldData = trim($fieldData);
        }
        if ($fieldData !== null && (($fieldData == '') || (intval($fieldData) < $this->getMin()) || (intval($fieldData) > $this->getMax()))) {
            return $this;
        }

        return false;
    }
}
