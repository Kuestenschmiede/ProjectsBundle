<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GRadioGroupField extends C4GBrickField
{
    private $turnButton = true;
    private $showButtons = false;
    private $clearGroupText = '';
    private $addNameToId = true;
    private $withoutScripts = false;
    private $timeButtonSpecial = false;
    private $saveAsArray = false;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::RADIOGROUP)
    {
        parent::__construct($type);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);

        $value = $this->generateInitialValue($data, $this->isSaveAsArray());
        $is_frozen = $dialogParams->isFrozen();
        $result = '';
        $additionalInputClass = $this->showButtons ? ' c4g__btn-check' : '';
        $additionalLabelClass = $this->showButtons ? ' c4g__btn c4g__btn-radio' : '';

        $fieldName = $this->getFieldName();
        if ($this->getAdditionalID() || $this->getAdditionalID() == '0') {
            $fieldName = $this->getFieldName() . '_' . $this->getAdditionalID();
        }

        
        $id = $this->createFieldID();

        $changeAction = '';
        if ($this->isCallOnChange()) {
            $changeAction = ' onchange="' . $this->getCallOnChangeFunction() . '" ';
        }

        $condition = $this->createConditionData($fieldList, $data);

        $this->setWithoutDescriptionLineBreak(true);

        $viewType = $dialogParams->getViewType();
        if ($viewType && (
                ($viewType == C4GBrickViewType::PUBLICVIEW) ||
                ($viewType == C4GBrickViewType::GROUPVIEW) ||
                ($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                ($viewType == C4GBrickViewType::MEMBERVIEW) ||
                ($viewType == C4GBrickViewType::PUBLICUUIDVIEW) ||
                (($viewType == C4GBrickViewType::GROUPPROJECT) && $is_frozen))
        ) {
            $required = 'disabled readonly';
        }

        if ($this->isMandatory()) {
            $required = 'required';
        }

        if (!$this->isEditable()) {
            if ($required != '') {
//                $required .= " disabled readonly";
            } else {
                $required = 'disabled readonly';
            }
        }

        if ($this->isSort()) {
            $options = ArrayHelper::array_sort($this->getOptions(), 'name');
        } else {
            $options = $this->getOptions();
        }

        if ($this->isShowIfEmpty() || (!$this->isShowIfEmpty() && count($options) > 0)) {
            $addToFieldset = ' class="c4g__form-radio-group radio-group-' . $fieldName . '"';
            $option_results = '';
            $object_id = -1;
            foreach ($options as $option) {
                $option_id = trim($option['id']);
                if ($this->addNameToId) {
                    $name = '_' . $id;
                    $option_name = $fieldName . $option_id;
                    $for = $fieldName . $option_id;
                } else {
                    $name = $id;
                    $option_name = $option_id;
                    $for = $option_name;
                }
                $type_caption = $option['name'];

                if (key_exists('objects', $option) && $option['objects'] && count($option['objects']) > 0) {
                    $cnt = 0;
                    foreach ($option['objects'] as $key => $object) {
                        $object_id = ($cnt == 0) ? $option['objects'][$key]['id'] : $object_id . '-' . $option['objects'][$key]['id'];
                        $cnt++;
                    }
                }
                $optionAttributes = key_exists('attribtes', $option) && $option['attributes'] ? ' ' . $option['attributes'] . ' ' : '';

                $onClick = "document.getElementById('" . $id . "') && document.querySelector('input[name=_" . $id . "]:checked') ? document.getElementById('" . $id . "').value = document.querySelector('input[name=_" . $id . "]:checked').value : '';" . $this->getCallOnChangeFunction() . ";";
                if ($object_id && intval($object_id) != -1) {
                    $object_class = 'class="c4g__form-check-input radio_object_' . $object_id . $additionalInputClass . '" ';
                } else {
                    $object_class = 'class="c4g__form-check-input' . $additionalInputClass . '" ';
                }

                $option_results = $option_results . '<div class="c4g__form-check"><input type="radio" ' . $object_class . 'id="' . $option_name . '" name="' . $name . '" ' . $optionAttributes . ' ' . $changeAction . ' onclick="' . $onClick . '" data-object="" value="' . $option_id . '" ' . (($value == $option_id) ? 'checked' : '') . ' /><label class="c4g__form-check-label' . $additionalLabelClass . '" for="' . $for . '" >' . $type_caption . '</label></div>';
            }

            if (!$option_results) {
                $option_results = '<div class="c4g__form-radio-group_clear">' . $this->clearGroupText . '</div>';
            }

            $conditionPrepare = $condition['conditionPrepare'];

            $attributes = $this->getAttributes() ? ' ' . $this->getAttributes() . ' ' : '';

            if ($this->withoutScripts) {
                $result .= $this->generateC4GFieldHTML($condition, '<div class="c4g__form-radio-group_wrapper" ' . $condition['conditionPrepare'] . '>' .
                    '<input type="hidden" name="' . $fieldName . '" value="' . $value . '" id="' . $id . '"  ' . $required . ' ' . $conditionPrepare . ' ' . 'class="formdata ' . $id . $attributes . '">' .
                    '<label class="c4g__form-radio-group_label"' . $conditionPrepare . '>' . $this->addC4GField(null, $dialogParams, $fieldList, $data, '</label>' .
                        '<fieldset' . $addToFieldset . '>' .
                        $option_results .
                        '</fieldset>' .
                        '</div>'));
            } else {
                $result .= $this->generateC4GFieldHTML($condition, '<div class="c4g__form-radio-group_wrapper" ' . $condition['conditionPrepare'] . '>' .
                    '<input type="hidden" name="' . $fieldName . '" value="' . $value . '" id="' . $id . '"  ' . $required . ' ' . $conditionPrepare . ' ' . 'class="formdata ' . $id . $attributes . '">' .
                    '<label class="c4g__form-radio-group_label"' . $conditionPrepare . '>' . $this->addC4GField(null, $dialogParams, $fieldList, $data, '</label>' .
                        '<fieldset' . $addToFieldset . '>' .
                        $option_results .
                        '</fieldset><span class="reset_c4g__form-radio-group"></span>' .
                        '</div><script>function resetRadioGroup(){ document.querySelector("input[name=\'_' . $id . '\']").removeAttribute(\'checked\');document.getElementById("' . $id . '") ? document.getElementById("' . $id . '").value = 0 : ""; };</script>'));
            }
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

        $result = null;
        $conditions = $this->getCondition();
        if (($conditions) && ($this->getConditionType() != C4GBrickConditionType::BOOLSWITCH)) {
            $found = false;
            foreach ($conditions as $condition) {
                if ($condition->getType() == C4GBrickConditionType::VALUESWITCH) {
                    $conditionField = $condition->getFieldName();
                    $conditionValue = $condition->getValue();

                    $conFieldValue = $dlgValues[$conditionField];
                    if ($conditionValue == $conFieldValue) {
                        $found = true;

                        break;
                    }
                } else {
                    if ($condition->getType() == C4GBrickConditionType::METHODSWITCH) {
                        $conditionField = $condition->getFieldName() ?: -1;
                        $conditionFunction = $condition->getFunction() ?: -1;
                        $conditionModel = $condition->getModel();

                        if ($conditionField && $conditionModel && $conditionFunction) {
                            if ($this->timeButtonSpecial) {
                                $conFieldValue = strtotime($dlgValues[$conditionField]);
                            }
                            $found = $conditionModel::$conditionFunction($conFieldValue);
                            if ($found) {
                                break;
                            }
                        }
                    }
                }
            }
            if (!$found) {
                return;
            }
        }

        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $dlgValue = $dlgValues[$this->getFieldName() . '_' . $additionalId];
        } else {
            $dlgValue = $dlgValues[$this->getFieldName()];
        }

        //compare for C4GMatching
        if ($this->isSearchField()) {
            if ($dbValue != $dlgValue) {
                $tmpValue = \Contao\StringUtil::deserialize($dlgValue);
                if (strlen($tmpValue[0]) > 0) {
                    $dlgValue = $tmpValue[0];
                } else {
                    $dbValue = C4GBrickCommon::translateSelectOption($dbValue, $this->getOptions());
                }
            }
        }
        if ($dbValue != $dlgValue) {
            if ($dbValue == -1 || $dbValue == 0) {
                $dbValue = '';
            }

            if ($dlgValue == -1 || $dlgValue == 0) {
                $dlgValue = '';
            }
            if ($dbValue != $dlgValue) {
                $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
            }
        }

        return $result;
    }

    /**
     * Public method that will be called to view the value
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        if ($this->timeButtonSpecial) {
            if (is_numeric($value)) {
                $timeFormat = $GLOBALS['TL_CONFIG']['timeFormat'];

                return date($timeFormat, $value);
            }
        }

        return C4GBrickCommon::translateSelectOption($value, $this->getOptions());
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues)
    {
        $fieldName = $this->getFieldName();
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldName = $fieldName . '_' . $additionalId;
        }
        $fieldData = $dlgValues[$fieldName];
        $conditions = $this->getCondition();
        if (($conditions) && ($this->getConditionType() != C4GBrickConditionType::BOOLSWITCH)) {
            $found = false;

            foreach ($conditions as $condition) {
                if ($condition->getType() == C4GBrickConditionType::METHODSWITCH) {
                    $conditionField = $condition->getFieldName() ?: -1;
                    $conditionFunction = $condition->getFunction() ?: -1;
                    $conditionModel = $condition->getModel();

                    if ($conditionField && $conditionModel && $conditionFunction) {
                        $conFieldValue = $dlgValues[$conditionField];
                        $found = $conditionModel::$conditionFunction($conFieldValue);
                        if ($found) {
                            break;
                        }
                    }
                } elseif ($condition->getType() == C4GBrickConditionType::VALUESWITCH) {
                    $conditionField = $condition->getFieldName();
                    $conditionValue = $condition->getValue();
                    if ($conditionField) {
                        $conFieldValue = $dlgValues[$conditionField];
                        $found = $conditionValue == $conFieldValue ? true : false;
                        if ($found) {
                            break;
                        }
                    }
                }
            }

            if (!$found) {
                return null;
            }
        }

        if ($this->isSaveAsArray()) {
            $fieldData = serialize([$fieldData]);
        }

        return $fieldData;
    }

    /**
     * Returns false if the field is not mandatory or if it is mandatory but its conditions are not met.
     * Otherwise it checks whether the field has a valid value and returns the result.
     * @param array $dlgValues
     * @return bool|C4GBrickField
     */
    public function checkMandatory($dlgValues)
    {
        //$this->setSpecialMandatoryMessage($this->getFieldName()); //Useful for debugging
        if (!$this->isMandatory()) {
            return false;
        } elseif (!$this->isDisplay()) {
            return false;
        } elseif ($this->getCondition()) {
            foreach ($this->getCondition() as $con) {
                $fieldName = $con->getFieldName();
                if (!$con->checkAgainstCondition($dlgValues[$fieldName])) {
                    return false; //todo shouldn't happend, better error handling.
                }
            }
        }
        $fieldName = $this->getFieldName();
        $fieldData = $dlgValues[$fieldName];
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldData = $dlgValues[$fieldName . '_' . $additionalId];
        }
        if (is_string($fieldData)) {
            $fieldData = trim($fieldData);
        }
        if (($fieldData == null) || ($fieldData) == '') {
            return $this;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public function isTurnButton()
    {
        return $this->turnButton;
    }

    /**
     * @param $turnButton
     * @return $this
     */
    public function setTurnButton($turnButton)
    {
        $this->turnButton = $turnButton;

        return $this;
    }

    /**
     * @return string
     */
    public function getClearGroupText()
    {
        return $this->clearGroupText;
    }

    /**
     * @param $clearGroupText
     * @return $this
     */
    public function setClearGroupText($clearGroupText)
    {
        $this->clearGroupText = $clearGroupText;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAddNameToId(): bool
    {
        return $this->addNameToId;
    }

    /**
     * @param bool $addNameToId
     */
    public function setAddNameToId(bool $addNameToId): void
    {
        $this->addNameToId = $addNameToId;
    }

    /**
     * @return bool
     */
    public function isWithoutScripts(): bool
    {
        return $this->withoutScripts;
    }

    /**
     * @param bool $withoutScripts
     */
    public function setWithoutScripts(bool $withoutScripts): void
    {
        $this->withoutScripts = $withoutScripts;
    }

    /**
     * @return bool
     */
    public function isTimeButtonSpecial(): bool
    {
        return $this->timeButtonSpecial;
    }

    /**
     * @param bool $timeButtonSpecial
     */
    public function setTimeButtonSpecial(bool $timeButtonSpecial): void
    {
        $this->timeButtonSpecial = $timeButtonSpecial;
    }

    /**
     * @return bool
     */
    public function isShowButtons(): bool
    {
        return $this->showButtons;
    }

    /**
     * @param bool $showButtons
     */
    public function setShowButtons(bool $showButtons): void
    {
        $this->showButtons = $showButtons;
    }

    /**
     * @return bool
     */
    public function isSaveAsArray(): bool
    {
        return $this->saveAsArray;
    }

    /**
     * @param bool $saveAsArray
     */
    public function setSaveAsArray(bool $saveAsArray): void
    {
        $this->saveAsArray = $saveAsArray;
    }
}
