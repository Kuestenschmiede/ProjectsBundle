<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GTrixEditorField extends C4GBrickField
{
    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return array|string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $value = $this->generateInitialValue($data);
        $value = str_replace('"', '&quot;', $value);

        $result = '';

        if ($this->isShowIfEmpty() || !empty(trim($value))) {
            $condition = $this->createConditionData($fieldList, $data);

            if ($this->isEditable()) {
                $fieldData = '<input id="' . $id . '" class="formdata c4g-editor-trix ui-corner-all" name="' .
                    $this->getFieldName() . '" value="' . $value . '" type="hidden" name="content">' .
                    '<trix-editor class="trix-content '.$this->getStyleClass().'" input="' . $id . '"></trix-editor>';
            } else {
                $fieldData = '<div disabled ' . $condition['conditionPrepare'] . ' id="' . $id .
                    '" class="formdata c4g-editor-disabled ' . $id . ' ui-corner-all">' .
                    html_entity_decode($value) . ' </div>';
            }

            $condition = $this->createConditionData($fieldList, $data);

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $fieldData);
        }

        return $result;
    }

    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare|null
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
