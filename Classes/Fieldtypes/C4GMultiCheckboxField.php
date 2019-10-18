<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use Contao\StringUtil;

class C4GMultiCheckboxField extends C4GBrickField
{
    private $modernStyle = false;
    private $serializeResult = true;

    /**
     * @param $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = $this->createFieldID();

        $title = $this->getTitle();
        $size = $this->getSize();
        $value = $this->generateInitialValue($data);
        if ($this->isSort()) {
            $options = C4GBrickCommon::array_sort($this->getOptions(), 'name');
        } else {
            $options = $this->getOptions();
        }
        $result = '';
        $required = '';

        $values = array();
        if ($value) {
            if ($this->serializeResult) {
                $tmpArray = StringUtil::deserialize(html_entity_decode($value));
            } else {
                $tmpArray = $value;
            }
            if ($tmpArray) {
                foreach ($tmpArray as $tmpKey => $tmpValue) {
                    $values[$tmpValue] = $tmpKey;
                }
            }
        }

        $withOptions = false;
        foreach ($options as $option) {
            $option_id = $option['id'];
            if (isset($values[$option_id])) {
                $withOptions = true;
                break;
            }
        }

        if ($this->isShowIfEmpty() || (!$this->isShowIfEmpty() && $withOptions)) {
            $condition = $this->createConditionData($fieldList, $data);
            $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);

            $spanStart = '';
            $spanEnd = '';
            $conditionStart =
                '<div id="c4g_condition" '
                . 'class="formdata"'
                . $condition['conditionName']
                . $condition['conditionType']
                . $condition['conditionValue']
                . $condition['conditionDisable']
                . '>';
            if($this->isModernStyle() == false){
                $div =  $conditionStart.'<div class="c4g_multicheckbox formdata" '.$condition['conditionPrepare'];
                $spanStart =  '<span class="c4g_checkbox">';
                $spanEnd = '</span>';
            } else {
                $div = $conditionStart.'<div class="c4g_multicheckbox_modern formdata" '.$condition['conditionPrepare'];
            }

            $result = $div . '>';
            $result .= $this->addC4GFieldLabel($id, $title, $this->isMandatory(), $condition, $fieldList, $data, $dialogParams);

            $viewType = $dialogParams->getViewType();
            if ($viewType && (
                    ($viewType == C4GBrickViewType::PUBLICVIEW) ||
                    ($viewType == C4GBrickViewType::GROUPVIEW) ||
                    ($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                    ($viewType == C4GBrickViewType::MEMBERVIEW) ||
                    ($viewType == C4GBrickViewType::PUBLICUUIDVIEW) ||
                    (($viewType == C4GBrickViewType::GROUPPROJECT) && $dialogParams->isFrozen()))
            ) {
                $required = "disabled readonly";
            }

            if ($this->isMandatory()) {
                $required = "required";
            }

            if (!$this->isEditable()) {
                if ($required != "") {
                    $required .= " disabled readonly";
                } else {
                    $required = "disabled readonly";
                }
            }


            //$required .= ' style="display:none;"';

            foreach ($options as $option) {
                $option_id = $option['id'];
                if ($values && !isset($values[$option_id]) && !$this->isShowIfEmpty()) {
                    continue;
                }
                $type_caption = $option['name'];
                $optionId = $this->getFieldName() . '|' . $option_id;
                $condition['conditionPrepare'] = '';
                $result .= $spanStart.
                   '<input type="checkbox" id="c4g_' . $optionId . '" ' . $required . ' class="formdata c4g_display_none" size="' . $size . '" name="' . $optionId . '" value="' . $optionId . '"' .
                    ($values && isset($values[$option_id]) ? ' checked="checked"' : '') . '">' . $this->addC4GFieldLabel('c4g_' . $optionId, $type_caption, false, $condition, $fieldList, $data, $dialogParams, false, true)
                    .$spanEnd;
            }
            $result .= $description;
            $result .= '</div><br></div>';
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
        $dbValue = $dbValues->$fieldName;
        $field_content = $dbValue;
        $result = array();
        if ($field_content) {
            $cbArray = null;
            $tmpArray = StringUtil::deserialize($field_content);
            if ($tmpArray) {
                foreach ($tmpArray as $tmpKey => $tmpValue)
                {
                    $cbArray[$tmpValue] = $tmpKey;
                }
            }
            //$cbArray = array_flip(unserialize(html_entity_decode($field_content)));
        } else {
            $cbArray = null;
        }
        $prefix = $this->getFieldName() . '|';
        $dlgArr = array();
        foreach($dlgValues as $valueKey=>$dlgValue) {
            if (C4GUtils::startsWith($valueKey, $prefix))
            {
                $pos = strpos($valueKey, '|');
                $key = substr($valueKey, $pos + 1);
                $options = $this->getOptions();
                foreach($options as $option) {
                    if ($option['id'] == $key) {
                        $name = $option['name'];
                    }
                }
                if (($dlgValue && (is_bool($dlgValue) || $dlgValue === 'true'))) {
                    $dlgArr[$key] = true;
                    if (!isset($cbArray[$key])) {
                        $result[] = new C4GBrickFieldCompare($this, '', $name);
                    }
                }
            }

        }
        if ($cbArray) {
            foreach($cbArray as $key=>$cbValue) {
                $options = $this->getOptions();
                foreach($options as $option) {
                    if ($option['id'] == $key) {
                        $name = $option['name'];
                    }
                }
                if (!isset($dlgArr[$key]) && !$this->isSearchField()) {
                    $result[] = new C4GBrickFieldCompare($this, $name, '');
                }
                else {
                    //change format of the data like in the DB - exception for C4GMatching
                    $dlgValues = serialize($tmpArray);
                    if(strcmp($field_content, $dlgValues) != 0) {
                        $result[] = new C4GBrickFieldCompare($this, $field_content, $dlgValues);
                    }
                }
            }
        }
        //query for C4GBrickMatching
        if(sizeof($result) == 0 && $this->isSearchField()) {
            if(is_array($dlgValues)) {
                $dlgValue = StringUtil::deserialize($dlgValues[$fieldName]);
            } else {
                $dlgValue = $dlgValues;
            }
            $dbValue = $dbValues->$fieldName;
            if(is_array($dlgValue)){
                if(is_array($dbValue)) {
                    $diff = array_diff($dbValue, $dlgValue);
                    if ($diff) {
                        $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                    }
                } else {
                    $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                }
            }
            else if(strcmp($dbValue, $dlgValue) != 0) {
                    $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
                }
        }
        return $result;
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues)
    {
//        $fieldData = $dlgValues[$this->getFieldName()];
        $fieldName = $this->getFieldName();
        $valueArr = array();
        $prefix = $fieldName . '|';// . $dlgValues['id'];

        foreach($dlgValues as $valueKey=>$dlgValue) {
            if (C4GUtils::startsWith($valueKey, $prefix) && ($dlgValue && (is_bool($dlgValue) || $dlgValue === 'true')))
            {
                $pos = strpos($valueKey, '|');
                $key = substr($valueKey, $pos + 1);
                $valueArr[] = trim($key);
            }
        }
        $fieldData = [];
        if(sizeof($valueArr) > 0) {
            if ($this->serializeResult) {
                $fieldData = serialize($valueArr);
            } else {
                $fieldData = $valueArr;
            }
        }
        return $fieldData;
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        return C4GBrickCommon::translateSelectOption($value, $this->getOptions());
    }

    /**
     * Returns false if the field is not mandatory or if it is mandatory but its conditions are not met.
     * Otherwise it checks whether the field has a valid value and returns the result.
     * @param array $dlgValues
     * @return bool|C4GBrickField
     */

    public function checkMandatory($dlgValues)
    {
        //$this->setSpecialMandatoryMessage($this->getFieldName());   //Useful for debugging
        if (!$this->isMandatory()) {
            return false;
        } elseif(!$this->isDisplay()) {
            return false;
        } elseif ($this->getCondition()) {
            foreach ($this->getCondition() as $con) {
                $fieldName = $con->getFieldName();
                if (!$con->checkAgainstCondition($dlgValues[$fieldName])) {
                    return false;
                }
            }
        }
        $fieldData = '';
        $fieldName = $this->getFieldName();
        foreach ($dlgValues as $name => $dlgValue) {
            if (C4GUtils::startsWith($name, $fieldName.'|')) {
                if ($dlgValue == true && $dlgValue !== 'false') {
                    $fieldData = $name;
                    break;
                }
            }
        }
        if (is_string($fieldData)) {
            $fieldData = trim($fieldData);
        }
        if (($fieldData == null) || ($fieldData) == '') {
            return $this;
        }
    }

    /**
     * @return boolean
     */
    public function isModernStyle()
    {
        return $this->modernStyle;
    }

    /**
     * @param $modernStyle
     * @return $this
     */
    public function setModernStyle($modernStyle)
    {
        $this->modernStyle = $modernStyle;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSerialize()
    {
        return $this->serializeResult;
    }

    /**
     * @param $serializeResult
     * @return $this
     */
    public function setSerialize($serializeResult)
    {
        $this->serializeResult = $serializeResult;
        return $this;
    }

    /**
     * Helper function to create an option array compatible with setOptions()
     * @param array $array
     * @return array
     */
    public function createOptionArray(array $array) {
        $options = [];
        $count = 1           ;
        foreach ($array as $entry) {
            $options[] = ['id' => strval($count), 'name' => strval($entry)];
            $count += 1;
        }
        return $options;
    }

    public function setOptionsFromArray(array $array) {
        return $this->setOptions($this->createOptionArray($array));
    }

}