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

use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GRadioGroupField extends C4GBrickField
{
    private $turnButton = false;
    private $clearGroupText = '';
    private $addNameToId = true;

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $is_frozen = $dialogParams->isFrozen();
        $result = '';

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

        $option_results = '';
        foreach ($options as $option) {
            $option_id = $option['id'];
            if ($this->addNameToId) {
                $name = '_' . $id;
                $option_name = $fieldName . $option_id;
                $for = $fieldName . $option_id;
                $addToFieldset = ' class="c4g_brick_radio_group"';
            //$addToWrapper = '';
            } else {
                $name = $id;
                $option_name = $option_id;
                $for = $option_name;
                $addToFieldset = ' class="c4g_brick_radio_group"';
                //$addToFieldset = ' id="'.$name.'" class="c4g_brick_radio_group formdata"';
                //$addToWrapper = '';
            }
            $type_caption = $option['name'];

            if ($option['objects'] && count($option['objects']) > 0) {
                $cnt = 0;
                foreach ($option['objects'] as $key=>$object) {
                       $object_id = ($cnt == 0) ? $option['objects'][$key]['id'] : $object_id.'-'.$option['objects'][$key]['id'];
                       $cnt++;
                }
            }
            $optionAttributes = $option['attributes'] ? ' ' . $option['attributes'] . ' ': '';
            if ($this->turnButton) {
                if ($object_id && $object_id != -1) {
                    $object_class = 'class="radio_object_' . $object_id . '" ';
                }/* else {
                    $object_class = 'class="radio_object_disabled" disabled ';
                }*/

                $option_results = $option_results . '<div class="radio_element rb_turned"><input type="radio" ' . $object_class . 'id="' . $option_name . '" name="' . $name . '" ' . $optionAttributes . $required . ' ' . $changeAction . ' value="' . $option_id . '" ' . (($value == $option_id) ? 'checked' : '') . ' /><label class="full lbl_turned" for="' . $for . '" >' . $type_caption . '</label></div>';
            } else {
                if ($object_id && $object_id != -1) {
                    $object_class = 'class="radio_object_' . $object_id . '" ';
                }/* else {
                    $object_class = 'class="radio_object_disabled" disabled ';
                }*/

                $option_results = $option_results . '<div class="radio_element"><label class="full" for="' . $for . '" >' . $type_caption . '</label><input type="radio" ' . $object_class . 'id="' . $option_name . '" name="' . $name . '" ' . $optionAttributes . $required . ' ' . $changeAction . ' value="' . $option_id . '" ' . (($value == $option_id) ? 'checked' : '') . ' /></div>';
            }
        }

        if (!$option_results) {
            $option_results = '<div class="c4g_brick_radio_group_clear">' . $this->clearGroupText . '</div>';
        }

        $conditionPrepare = $condition['conditionPrepare'];

        $attributes = $this->getAttributes() ? ' ' . $this->getAttributes() . ' ': '';

        $result .= $this->generateC4GFieldHTML($condition, '<div class="c4g_brick_radio_group_wrapper" ' . $condition['conditionPrepare'] . '>' .
                       '<input type="hidden" name="' . $fieldName . '" value="' . $value . '" id="' . $id . '"  ' . $required . ' ' .$conditionPrepare. ' ' . 'class="formdata ' . $id . $attributes . '">' .
                       '<label '.$conditionPrepare.'>' . $this->addC4GField(null, $dialogParams, $fieldList, $data, '</label>' .
                       '<fieldset' . $addToFieldset . '>' .
                       $option_results .
                       '</fieldset><span class="reset_c4g_brick_radio_group"></span><script>function resetRadioGroup(){ jQuery("input[name=\'_' . $id . '\']").removeAttr(\'checked\');jQuery("#' . $id . '").val(0); };jQuery(document).ready(function(){jQuery("input[name=\'_' . $id . '\']").on("click",function(){jQuery("#' . $id . '").val(jQuery("input[name=\'_' . $id . '\']:checked").val())})});</script>' .
                       '</div>'));

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
                        $conditionField = $condition->getFieldName();
                        $conditionFunction = $condition->getFunction();
                        $conditionModel = $condition->getModel();

                        if ($conditionField && $conditionModel && $conditionFunction) {
                            $conFieldValue = strtotime($dlgValues[$conditionField]);
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
                $tmpValue = unserialize($dlgValue);
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
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
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
            //ToDo reservation
            foreach ($conditions as $condition) {
                if ($condition->getType() == C4GBrickConditionType::METHODSWITCH) {
                    $conditionField = $condition->getFieldName();
                    $conditionFunction = $condition->getFunction();
                    $conditionModel = $condition->getModel();

                    if ($conditionField && $conditionModel && $conditionFunction) {
                        $conFieldValue = $dlgValues[$conditionField];
                        $found = $conditionModel::$conditionFunction($conFieldValue);
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
                    return false;
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
}
