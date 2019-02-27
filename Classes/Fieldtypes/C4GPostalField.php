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

use con4gis\ProjectsBundle\Classes\Common\C4GBrickRegEx;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldNumeric;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GPostalField extends C4GBrickFieldNumeric
{
    protected $pattern = C4GBrickRegEx::POSTAL;

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<input type="text" pattern="'. $this->pattern .'" size="5"' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" class="formdata ' . $id . '" name="' . $this->getFieldName() . '" title="' . $this->getTitle() . '" value="' . $value . '">');
        }
        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare
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