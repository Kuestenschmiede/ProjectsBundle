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

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldText;
use Contao\Controller;

class C4GMultiColumnField extends C4GBrickField
{
    protected $fields = [];
    protected $addButtonLabel = "Hinzufügen";
    protected $removeButtonLabel = "Entfernen";

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty(trim($value))) {

            $condition = $this->createConditionData($fieldList, $data);

            $fieldData = "<div ".$condition['conditionPrepare'].">";
            $fieldData .= '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="hidden" id="' . $id .
                '" class="formdata ' . $id . '" name="' . $this->getFieldName() . '" value="' . $value . '">';
            $fieldData .= "<table class=\"c4g_multicolumn\"><tbody>";
            $head = "<tr>";
            $row = "<tr>";
            foreach ($this->fields as $field) {
                $head .= "<th data-name=\"".$field['name']."\">".$field['label']."</th>";
                $row .= "<td><input name=\"".$field['name']."\" ".
                    "type=\"".$field['type']."\" ".
                    "oninput=\"onMultiColumnInput(this, event);\"></td>";
            }
            $head .= "</tr>";
            $row .= "<td><button class=\"ui-button ui-corner-all ui-widget\" ".
                "onclick=\"onAddRowButtonClick(this, event);\">"
                .$this->addButtonLabel. "</button></td>";
            $row .= "<td><button class=\"ui-button ui-corner-all ui-widget\"".
                " onclick=\"onRemoveRowButtonClick(this, event);\">".
                $this->removeButtonLabel."</button></td>";
            $row .= "</tr>";
            $fieldData .= $head;
            $fieldData .= $row;
            $fieldData .= "</tbody></table>";
            $fieldData .= "</div>";


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

    public function createFieldData($dlgValues)
    {
        return parent::createFieldData($dlgValues); // TODO: Change the autogenerated stub
    }

    public function addInputField(string $name, string $type, string $label) {
        $this->fields[] = [
            'name' => $name,
            'type' => $type,
            'label' => $label
        ];
        return $this;
    }
}