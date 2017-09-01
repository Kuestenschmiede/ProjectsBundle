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
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GNominatimAddressField extends C4GBrickField
{
    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $database = $additionalParams['database'];
        $fieldName = $this->getFieldName();
        $id = "c4g_" . $fieldName;
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams);
        $latitude=$data->latitude;
        $longitutde=$data->longitude;
        $this->setLatitudeField($latitude);
        $this->setLongitudeField($longitutde);
        $contentId = $this->getContentId();
        $value = C4GBrickCommon::convert_coordinates_to_address($this->getLongitudeField(), $this->getLatitudeField(), $contentId, $database);
        $result = '';
        if ($this->isShowIfEmpty() || !empty($value)){

            $condition = $this->createConditionData($fieldList, $data);
            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<input ' . $required . ' '.$condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata ' . $id . '" name="' . $fieldName . '" value="' . $value . '">');
        }
        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
        // TODO: Implement compareWithDB() method.
    }
}