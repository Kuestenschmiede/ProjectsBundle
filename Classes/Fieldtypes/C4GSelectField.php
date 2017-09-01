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

class C4GSelectField extends C4GBrickField
{
    private $chosen = false;
    private $minChosenCount = 7;
    private $placeholder = ''; // GUI placeholder text
    private $emptyOptionLabel = '';


    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        if ($this->getAdditionalID()) {
            $this->setFieldName($this->getFieldName() .'_'.$this->getAdditionalID());
        }

        $id = $this->createFieldID();

        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $changeAction = '';

        if ($this->isCallOnChange()) {
            $changeAction = 'onchange="C4GCallOnChange(this)"';
        }

        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $options = $this->getSelectOptions($data, $value, $condition);
            $class = 'formdata';
            if ($this->isChosen() && (count($this->getOptions()) >= $this->getMinChosenCount())) {
                if (strpos($required, "disabled")) {
                    $class = $class.' chzn-select-disabled';
                } else {
                    $class = $class.' chzn-select'; // class 'chzn-select' triggers javascript to make the select list a filterable combobox
                }
            }

            $placeholder = $this->placeholder ? $this->placeholder :$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['PLACEHOLDER_SELECT'];
            $result = $this->addC4GField(
                $condition,
                $dialogParams, $fieldList, $data,
                '<select id="' . $id . '" ' . $required . ' ' . $condition['conditionPrepare']
                . ' class="'.$class.'" name="' . $this->getFieldName() . '" ' . $changeAction . ' size="' . $this->getSize() . '"'
                . ' data-placeholder="'. $placeholder .'" '
                . '>'
                . $options
                . '</select>'
            );
        }
        return $result;
    }

    protected function getSelectOptions($data, $value, $condition)
    {
        $options = '';

        if ($this->isWithEmptyOption()) {
            $fieldOptions = $this->getOptions();
            if ($fieldOptions) {
                array_unshift($fieldOptions, array('id' => '-1', 'name' => $this->emptyOptionLabel));
//                $fieldOptions[-1] = array('id' => '', 'name' => '');
                $this->setOptions($fieldOptions);
            }
        }

        if ($data && $this->getLoadOptions()) {
            $model = $this->getModel();
            $keyField = $this->getKeyField();
            $idField = $this->getIdField();
            $nameField = $this->getNameField();

            $id = $data->id;
            $elements = $model::findby($keyField, $id);

            if ($this->isSort()) {
                $elements = C4GBrickCommon::array_sort($elements, $nameField);
            }


            foreach ($elements as $element) {
                if ((!$element->$idField) && (!$element->$nameField)) {
                    $option[] = array('id' => $value, 'name' => $value);
                }

                if ($this->isShowIfEmpty() || (!empty($element->$idField) && !empty($element->$nameField))) {
                    $option_id = $element->$idField;
                    $option_name = $element->$nameField;

                    if ($option_name == '') {
                        $option_name = $option_id;
                    }

                    $selected = '';
                    if ($value == $option_id) {
                        $selected = 'selected';
                    }
                    $options = $options . "<option " . $selected . " value=" . $option_id . ">" . $option_name . "</option>";
                }
            }
        } else {
            if (($this->getOptions())) {

                if ($this->isSort()) {
                    $selectoptions = C4GBrickCommon::array_sort($this->getOptions(), 'name');
                } else {
                    $selectoptions = $this->getOptions();
                }

                if (!in_array($value, $selectoptions)) {

                };


                foreach ($selectoptions as $option) {
                    if ((!$option['id']) && (!$option['name'])) {
                        $option[] = array('id' => $value, 'name' => $value);
                    }

                    if ($this->isShowIfEmpty() || (!empty($option['id']) && !empty($option['name']))) {
                        $option_id = $option['id'];
                        $option_name = $option['name'];

                        if ($option_name == '') {
                            $option_name = $option_id;
                        }

                        $selected = '';
                        if (($value == $option_id) && ($condition['conditionPrepare'] == '')) {
                            $selected = ' selected';
                        }
                        $options = $options . "<option" . $selected . " value=" . $option_id . ">" . $option_name . "</option>";
                    }
                }
            }
        }

        return $options;
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
                if (empty($condition)) {
                    continue;
                }

                if ($condition->getType() == C4GBrickConditionType::VALUESWITCH) {
                    $conditionField = $condition->getFieldName();
                    $conditionValue = $condition->getValue();

                    $conFieldValue = $dlgValues[$conditionField];
                    if ($conditionValue == $conFieldValue) {
                        $found = true;
                        break;
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
                if ($condition->getType() == C4GBrickConditionType::VALUESWITCH) {
                    $conditionField = $condition->getFieldName();
                    $conditionValue = $condition->getValue();

                    $conFieldValue = $dlgValues[$conditionField];
                    if ($conditionValue == $conFieldValue) {
                        $found = true;
                        $additionalId = $this->getAdditionalID();
                        if (!empty($additionalId)) {
                            $fieldData = $dlgValues[$this->getFieldName() . '_' . $additionalId];
                        }
                        break;
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
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        return C4GBrickCommon::translateSelectOption($value, $this->getOptions());
    }

    /**
     * @return bool
     */
    public function isChosen()
    {
        return $this->chosen;
    }

    /**
     * @param bool $chosen
     */
    public function setChosen($chosen)
    {
        $this->chosen = $chosen;
    }

    /**
     * @return int
     */
    public function getMinChosenCount()
    {
        return $this->minChosenCount;
    }

    /**
     * @param int $minChosenCount
     */
    public function setMinChosenCount($minChosenCount)
    {
        $this->minChosenCount = $minChosenCount;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
    }

    /**
     * @return string
     */
    public function getEmptyOptionLabel(): string
    {
        return $this->emptyOptionLabel;
    }

    /**
     * @param string $emptyOptionLabel
     */
    public function setEmptyOptionLabel(string $emptyOptionLabel)
    {
        $this->emptyOptionLabel = $emptyOptionLabel;
    }
}
