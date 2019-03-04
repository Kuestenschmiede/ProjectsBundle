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

class C4GCKEditor5Field extends C4GBrickField
{
    protected static $instances = 0;

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return array|string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_ckeditor_".self::$instances;
        self::$instances += 1;
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);

        if (!($this->getSize())) {
            $size = 15;
        } else {
            $size = $this->getSize();
        }

        $condition = $this->createConditionData($fieldList, $data);
        $fieldData = '<textarea ' . $required . ' ' . $condition['conditionPrepare'] . 'id="'.$id.'"' . ' class="formdata js-ckeditor' . ' ui-corner-all" name="' . $this->getFieldName() . '" cols="80" rows="'.$size.'" >' . $value . ' </textarea>';
        $fieldData .= '<script>ClassicEditor.create(document.querySelector( \'#'.$id.'\' )).catch( error => {console.error(error);});</script>';

        $result = $this->addC4GField($condition,$dialogParams,$fieldList,$data,$fieldData);
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