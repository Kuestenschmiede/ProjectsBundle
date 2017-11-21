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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

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

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $is_frozen = $dialogParams->isFrozen();
        $result = '';

        if ($this->getAdditionalID() || $this->getAdditionalID() == '0') {
            $this->setFieldName($this->getFieldName() .'_'.$this->getAdditionalID());
        }

        $id = $this->createFieldID();

        $changeAction = '';
        if ($this->isCallOnChange()) {
            $changeAction = ' onchange="'.$this->getCallOnChangeFunction().'" ';
        }

        $condition = $this->createConditionData($fieldList, $data);

        $this->setWithoutDescriptionLineBreak(true);

        $viewType = $dialogParams->getViewType();
        if ($viewType && (
                ($viewType == C4GBrickViewType::PUBLICVIEW) ||
                ($viewType == C4GBrickViewType::GROUPVIEW) ||
                ($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                ($viewType == C4GBrickViewType::MEMBERVIEW) ||
                (($viewType == C4GBrickViewType::GROUPPROJECT) && $is_frozen))
        ) {
            $required = "disabled readonly";
        }

        if ($this->isMandatory()) {
            $required = "required";
        }

        if (!$this->isEditable()) {
            if ($required != "") {
//                $required .= " disabled readonly";
            } else {
                $required = "disabled readonly";
            }
        }

        if ($this->isSort()) {
            $options = C4GBrickCommon::array_sort($this->getOptions(), 'name');
        } else {
            $options = $this->getOptions();
        }

        $option_results = '';
        foreach ($options as $option) {
            $option_id = $option['id'];
            $option_name = $this->getFieldName() . $option_id;
            $type_caption = $option['name'];
            $object_id = $option['object'];

            if ($this->turnButton) {
                if ($object_id && $object_id != -1) {
                    $object_class = 'class="radio_object_'.$object_id.'" ';
                } else {
                    //$object_class = 'class="radio_object_disabled" disabled ';
                }
                $option_results = $option_results.'<div class="radio_element rb_turned"><input type="radio" '.$object_class.'id="'.$option_name.'" name="_'.$id.'" '.$required.' '.$changeAction.' value="'.$option_id.'" '. (($value == $option_id) ? "checked" : "") .' /><label class="full lbl_turned" for="'.$option_name. '" >'  . $type_caption . '</label></div>';
            } else {
                if ($object_id && $object_id != -1) {
                    $object_class = 'class="radio_object_'.$object_id.'" ';
                } else {
                    //$object_class = 'class="radio_object_disabled" disabled ';
                }
                $option_results = $option_results.'<div class="radio_element"><label class="full" for="'.$option_name. '" >'  . $type_caption . '</label><input type="radio" '.$object_class.'id="'.$option_name.'" name="_'.$id.'" '.$required.' '.$changeAction.' value="'.$option_id.'" '. (($value == $option_id) ? "checked" : "") .' /></div>';
            }
        }

        if (!$option_results) {
            $option_results = '<div class="c4g_brick_radio_group_clear">'.$this->clearGroupText.'</div>';
        }

        $result .= '<div id="c4g_condition" '
                    . $condition['conditionName']
                    . $condition['conditionType']
                    . $condition['conditionValue']
                    . $condition['conditionFunction']
                    . $condition['conditionDisable'] . '>' .
                    '<div class="c4g_brick_radio_group_wrapper" '.$condition['conditionPrepare'].'>' .
                    '<input type="hidden" name="'.$this->getFieldName().'" value="' . $value . '" id="'.$id.'"  ' . $required . ' ' . $condition['conditionPrepare'] . ' '.'class="formdata ' . $id . '">' .
                    '<label>' . $this->addC4GField(null,$dialogParams,$fieldList,$data,'</label>' .
                    '<fieldset class="c4g_brick_radio_group">' .
                    $option_results .
                    '</fieldset><span class="reset_c4g_brick_radio_group"></span><script>function resetRadioGroup(){ jQuery("input[name=\'_'.$id.'\']").removeAttr(\'checked\');jQuery("#'.$id.'").val(0); };jQuery(document).ready(function(){jQuery("input[name=\'_'.$id.'\']").on("click",function(){jQuery("#'.$id.'").val(jQuery("input[name=\'_'.$id.'\']:checked").val())})});</script>'.
                    '</div></div>');

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
            foreach($conditions as $condition) {
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
                            if ($found)
                                break;
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
            $dlgValue = $dlgValues[$this->getFieldName().'_'.$additionalId];
        } else {
            $dlgValue = $dlgValues[$this->getFieldName()];
        }

        //compare for C4GMatching
        if($this->isSearchField()) {
            if($dbValue != $dlgValue) {
                $tmpValue = unserialize($dlgValue);
                if(strlen($tmpValue[0]) > 0) {
                    $dlgValue = $tmpValue[0];
                } else {
                    $dbValue = C4GBrickCommon::translateSelectOption($dbValue,$this->getOptions());
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
            if($dbValue != $dlgValue) {
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
        $fieldData = $dlgValues[$this->getFieldName()];
        $conditions = $this->getCondition();
        if (($conditions) && ($this->getConditionType() != C4GBrickConditionType::BOOLSWITCH)) {
            $found = false;
            foreach($conditions as $condition) {
                if ($condition->getType() == C4GBrickConditionType::METHODSWITCH) {
                    $conditionField = $condition->getFieldName();
                    $conditionFunction = $condition->getFunction();
                    $conditionModel = $condition->getModel();

                    if ($conditionField && $conditionModel && $conditionFunction) {
                        $conFieldValue = strtotime($dlgValues[$conditionField]);
                        $found = $conditionModel::$conditionFunction($conFieldValue);
                        if ($found) {
                            $additionalId = $this->getAdditionalID();
                            if (!empty($additionalId)) {
                                $fieldData = $dlgValues[$this->getFieldName() . '_' . $additionalId];
                            }
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
     * @return boolean
     */
    public function isTurnButton()
    {
        return $this->turnButton;
    }

    /**
     * @param boolean $turnButton
     */
    public function setTurnButton($turnButton)
    {
        $this->turnButton = $turnButton;
    }

    /**
     * @return string
     */
    public function getClearGroupText()
    {
        return $this->clearGroupText;
    }

    /**
     * @param string $clearGroupText
     */
    public function setClearGroupText($clearGroupText)
    {
        $this->clearGroupText = $clearGroupText;
    }
}