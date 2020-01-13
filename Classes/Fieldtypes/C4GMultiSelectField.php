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
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GMultiSelectField extends C4GBrickField
{
    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $fieldName = $this->getFieldName();
        $id = $this->createFieldID();
        $required = $this->generateRequiredString($fieldList, $data);

        $value = $this->generateInitialValue($data);
//        if ($this->isCallOnChange()) {
//            $changeAction = 'onchange="C4GCallOnChange(this)"';
//        }

        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);
            $options = $this->getSelectOptions($data, $value, $condition);

            $result = $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<select multiple id="' . $id . '" ' . $required . ' class="' . $condition['class'] . '" name="' . $fieldName . '"  size="'
                . $this->getSize() . '" >' . $options . '</select>');
        }

        return $result;
    }

    protected function getSelectOptions($data, $value, $condition)
    {
        $options = '';

        if ($this->isWithEmptyOption()) {
            $fieldOptions = $this->getOptions();
            if ($fieldOptions) {
                $fieldOptions[-1] = ['id' => '', 'name' => ''];
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

            $elements = C4GBrickCommon::array_sort($elements, $nameField);

            foreach ($elements as $element) {
                if ((!$element->$idField) && (!$element->$nameField)) {
                    $option[] = ['id' => $value, 'name' => $value];
                }

                if ($this->isShowIfEmpty() || !empty($element->$idField) && !empty($element->$nameField)) {
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
                $selectoptions = C4GBrickCommon::array_sort($this->getOptions(), 'name');

                if (!in_array($value, $selectoptions)) {
                };

                foreach ($selectoptions as $option) {
                    if ((!$option['id']) && (!$option['name'])) {
                        $option[] = ['id' => $value, 'name' => $value];
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
                        $options = $options . '<option' . $selected . ' value=' . $option_id . '>' . $option_name . '</option>';
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
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
    }
}
