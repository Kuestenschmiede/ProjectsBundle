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

class C4GSelectField extends C4GBrickField
{
    private $chosen = false;
    private $minChosenCount = 7;
    private $placeholder = ''; // GUI placeholder text
    private $emptyOptionLabel = '-';
    private $simpleTextWithoutEditing = false;
    private $defaultOptionId = '';
    private $initialCallOnChange = false;
    private $withOptionType = false;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::SELECT)
    {
        parent::__construct($type);
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $fieldName = $this->getFieldName();
        if ($this->getAdditionalID()) {
            $fieldName = $this->getFieldName() . '_' . $this->getAdditionalID();
        }

        $id = $this->createFieldID();

        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        $changeAction = '';

        if ($this->isCallOnChange()) {
            if ($this->getCallOnChangeFunction()) {
                $changeAction = 'onchange="' . $this->getCallOnChangeFunction() . '"';
                if ($this->isInitialCallOnChange()) {
                    $dialogParams->setOnloadScript($this->getCallOnChangeFunction());
                }
            } else {
                $changeAction = 'onchange="handleBrickConditions();"';
                if ($this->isInitialCallOnChange()) {
                    $onLoadScript = 'handleBrickConditions();';
                    $dialogParams->setOnloadScript($onLoadScript);
                }
            }
        }

        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            $options = $this->getSelectOptions($data, $value, $condition);
            $class = 'formdata c4g__form-select ';
            if ($this->isChosen() && (count($this->getOptions()) >= $this->getMinChosenCount())) {
                if (strpos($required, 'disabled')) {
                    $class = $class . ' chzn-select-disabled';
                } else {
                    $class = $class . ' chzn-select';
                }
                $onLoadScript = 'resizeChosen("c4g_' . $id . '_chosen");';
                $dialogParams->setOnloadScript($onLoadScript);
            }

            $placeholder = $this->placeholder ? $this->placeholder :$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['PLACEHOLDER_SELECT'];
            if ($this->isSimpleTextWithoutEditing()) {
                $initialValue = '';
                foreach ($this->getOptions() as $option) {
                    if ($option['id'] == $value) {
                        $initialValue = $option['name'];

                        break;
                    }
                }
                $fieldData = '<div ' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '">' . $initialValue . '</div>';
            } else {
                $fieldData = '<select id="' . $id . '" ' . $required . ' ' . $condition['conditionPrepare']
                    . ' class="' . $class . '" name="' . $fieldName . '" ' . $changeAction . ' size="' . $this->getSize() . '"'
                    . ' data-placeholder="' . $placeholder . '" ';
                if ($this->getInitialValue()) {
                    $fieldData .= 'value=' . $this->getInitialValue();
                }
                $fieldData .= '>'
                    . $options
                    . '</select>';
            }
            $result = $this->addC4GField(
                $condition,
                $dialogParams, $fieldList, $data, $fieldData
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
                if ($this->getRangeField()) {
                    $min = key_exists('min', $fieldOptions) ? $fieldOptions['min'] : 0;
                    $max = key_exists('max', $fieldOptions) ? $fieldOptions['max'] : 0;
                    array_unshift($fieldOptions, ['id' => '-1', 'name' => $this->emptyOptionLabel, 'min' => $min, 'max' => $max]);
                } elseif ($this->isWithOptionType()) {
                    $type = $fieldOptions['type'];
                    array_unshift($fieldOptions, ['id' => '-1', 'name' => $this->emptyOptionLabel, 'type => $type']);
                } else {
                    array_unshift($fieldOptions, ['id' => '-1', 'name' => $this->emptyOptionLabel]);
                }
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
                $elements = ArrayHelper::array_sort($elements, $nameField);
            }

            foreach ($elements as $element) {
                if ((!$element->$idField) && (!$element->$nameField)) {
                    $option[] = ['id' => $value, 'name' => $value, ''];
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
                    $options = $options . '<option ' . $selected . ' value=' . $option_id . '>' . $option_name . '</option>';
                }
            }
        } else {
            if (($this->getOptions())) {
                if ($this->isSort()) {
                    $selectoptions = ArrayHelper::array_sort($this->getOptions(), 'name');
                } else {
                    $selectoptions = $this->getOptions();
                }

                foreach ($selectoptions as $option) {
                    if ($this->getRangeField()) {
                        if ((!$option['id']) && (!$option['name'])) {
                            $option[] = ['id' => $value, 'name' => $value, 'min' => $option['min'], 'max' => $option['max']];
                        }
                    } elseif ($this->isWithOptionType()) {
                        if ((!$option['id']) && (!$option['name'])) {
                            $option[] = ['id' => $value, 'name' => $value, 'type' => $option['type']];
                        }
                    } else {
                        if ((!$option['id']) && (!$option['name'])) {
                            $option[] = ['id' => $value, 'name' => $value];
                        }
                    }

                    $optionAttributes = '';
                    if ($this->isShowIfEmpty() || (!empty($option['id']) && !empty($option['name']))) {
                        $option_id = $option['id'];
                        $option_name = $option['name'];
                        $optionAttributes = key_exists('attributes', $option) && $option['attributes'] ? ' ' . $option['attributes'] . ' ': '';

                        if ($option_name == '') {
                            $option_name = $option_id;
                        }

                        $selected = '';
                        if (($value == $option_id || (!$value && $this->defaultOptionId === $option_id)) && ($condition['conditionPrepare'] == '')) {
                            $selected = ' selected';
                        }

                        if ($this->getRangeField()) {
                            $min = $option['min'];
                            $max = $option['max'];
                            $options = $options . '<option' . $selected . $optionAttributes . ' min="' . $min . '" max="' . $max . '" value="' . $option_id . '">' . $option_name . '</option>';
                        } elseif ($this->isWithOptionType()) {
                            $type = $option['type'];
                            $options = $options . '<option' . $selected . $optionAttributes . ' type="' . $type . '" value="' . $option_id . '">' . $option_name . '</option>';
                        } else {
                            $options = $options . '<option' . $selected . $optionAttributes . ' value="' . $option_id . '">' . $option_name . '</option>';
                        }
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
            foreach ($conditions as $condition) {
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

            if ($dlgValue == -1 || $dlgValue == '-1' || $dlgValue == 0) {
                $dlgValue = '';
            }
            if ($dbValue != $dlgValue) {
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
        if ($fieldData == '-1') {
            $fieldData = '0';
        }

        return $fieldData;
    }

    /**
     * Public method that will be called to view the value
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
        //$this->setSpecialMandatoryMessage($this->getFieldName());     //Useful for debugging
        if (!$this->isMandatory()) {
            return false;
        } elseif (!$this->isDisplay()) {
            return false;
        } elseif ($this->getCondition()) {
            foreach ($this->getCondition() as $con) {
                $fieldName = $con->getFieldName();
                if ($this->getAdditionalID()) {
                    $fieldName = $fieldName . '_' . $this->getAdditionalID(); //ToDo
                }
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
        if (($fieldData == '-1') || ($fieldData == '')) {
            return $this;
        }

        return false;
    }

    /**
     * @param $data
     * @param $groupId
     * @return string
     */
    public function getC4GPopupField($data, $groupId)
    {
        if ($data[$this->getFieldName()]) {
            $styleClass = $this->getStyleClass();
            if ($this->isWithoutLabel()) {
                return '<div class=' . $styleClass . '>' . C4GBrickCommon::translateSelectOption($data[$this->getFieldName()], $this->getOptions()) . '</div>';
            }

            return '<p class=' . $styleClass . '><b>' . $this->getTitle() . '</b>: ' . C4GBrickCommon::translateSelectOption($data[$this->getFieldName()], $this->getOptions()) . '</p>';
        }

        return '';
    }

    /**
     * Helper function to create an option array compatible with setOptions()
     * @param array $array
     * @return array
     */
    public function createOptionArray(array $array)
    {
        $options = [];
        $count = 1           ;
        foreach ($array as $entry) {
            $options[] = ['id' => strval($count), 'name' => strval($entry)];
            $count += 1;
        }

        return $options;
    }

    public function setOptionsFromArray(array $array)
    {
        return $this->setOptions($this->createOptionArray($array));
    }

    /**
     * @return bool
     */
    public function isChosen()
    {
        return $this->chosen;
    }

    /**
     * @param $chosen
     * @return $this
     */
    public function setChosen($chosen)
    {
        $this->chosen = $chosen;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinChosenCount()
    {
        return $this->minChosenCount;
    }

    /**
     * @param $minChosenCount
     * @return $this
     */
    public function setMinChosenCount($minChosenCount)
    {
        $this->minChosenCount = $minChosenCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param $placeholder
     * @return $this
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmptyOptionLabel()
    {
        return $this->emptyOptionLabel;
    }

    /**
     * @param $emptyOptionLabel
     * @return $this
     */
    public function setEmptyOptionLabel($emptyOptionLabel)
    {
        $this->emptyOptionLabel = $emptyOptionLabel;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSimpleTextWithoutEditing(): bool
    {
        return $this->simpleTextWithoutEditing;
    }

    /**
     * @param bool $simpleTextWithoutEditing
     */
    public function setSimpleTextWithoutEditing(bool $simpleTextWithoutEditing): void
    {
        $this->simpleTextWithoutEditing = $simpleTextWithoutEditing;
    }

    /**
     * @return int
     */
    public function getDefaultOptionId(): string
    {
        return $this->defaultOptionId;
    }

    /**
     * @param int $defaultOptionId
     * @return C4GSelectField
     */
    public function setDefaultOptionId(string $defaultOptionId): C4GSelectField
    {
        $this->defaultOptionId = $defaultOptionId;

        return $this;
    }

    /**
     * @return bool
     */
    public function isInitialCallOnChange(): bool
    {
        return $this->initialCallOnChange;
    }

    /**
     * @param bool $initialCallOnChange
     */
    public function setInitialCallOnChange(bool $initialCallOnChange): void
    {
        $this->initialCallOnChange = $initialCallOnChange;
    }

    /**
     * @return bool
     */
    public function isWithOptionType(): bool
    {
        return $this->withOptionType;
    }

    /**
     * @param bool $withOptionType
     */
    public function setWithOptionType(bool $withOptionType): void
    {
        $this->withOptionType = $withOptionType;
    }
}
