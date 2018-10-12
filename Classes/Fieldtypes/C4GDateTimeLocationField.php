<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainer;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GDateTimeLocationField extends C4GBrickField
{
    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, C4Gcontainer $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $fieldName = $this->getFieldName();
        $content = $additionalParams['content'];
        $database = $additionalParams['database'];
        $id = "c4g_" . $fieldName;
        $title = $this->getTitle();
        $extModel = $this->getExternalModel();
        $extFieldName = $this->getExternalIdField();
        $latitudeField = $this->getLatitudeField();
        $longitudeField = $this->getLongitudeField();
        $addressField = $this->getAddressField();
        $withoutAddressReloadButton = $this->isWithoutAddressReloadButton();
        // $size = $field->getSize();
        $time = $data->loc_time;
        $required = $this->generateRequiredString( $data, $dialogParams);

        $address = null;
        if ($extModel && $extFieldName && $latitudeField && $longitudeField) {
            $lat = $data->$latitudeField;
            $lon = $data->$longitudeField;
//            $field->setLatitudeField($lat);
//            $field->setLongitudeField($lon);

            if ($addressField) {
                $address = $data->$addressField;
            }
        } else {
            $lon = $data->loc_geox;
            $lat = $data->loc_geoy;
        }

        $profile_id = null;
        if ($content) {
            $find = 'profile":"';
            $pos = strpos($content, $find);
            if ($pos > 0) {
                $str_profile_id = substr($content,$pos+10,4);
                if ($str_profile_id) {
                    $profile_id = intval($str_profile_id);
                }
            }
        }

        if ($profile_id) {
            if (!$address || (strlen($address) <= 3)) {
                $address = C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database);
            } else {
                $address = $data->$addressField;
            }
        }
        if ($address) {
            if($withoutAddressReloadButton)
            {
                $address_line =
                    '<div class="c4g_reverse_address"><input ' . $required . ' name="address" id="c4g_brick_geopicker_address_without_reloadbutton" value="' . $address . '" type="text" disabled="disabled" class="formdata" >';
            }
            else{
                $address_line =
                    '<div class="c4g_reverse_address"><input ' . $required . ' name="address" id="c4g_brick_geopicker_address" value="' . $address . '" type="text" disabled="disabled" class="formdata" >' .
                    '<button ' . $required . ' id="c4g_addressUpdateButton" onClick="C4GGeopickerAddress(\'' . $profile_id . '\')">Adresse neu laden</button></div>';
            }
        } else {
            $address_line = "";
        }

        $condition = $this->createConditionData($fieldList, $data);
        $description = $this->getC4GDescriptionLabel($this->getDescription(),$condition);

        $result =
//            $content . //Inhaltselement der Karte
            '<div '
            . $condition['conditionName']
            . $condition['conditionType']
            . $condition['conditionValue']
            . $condition['conditionDisable'] . '>
            <label class="c4g_brick_geopicker_label">' . $title . '</label>' .
            $address_line.
            '<input ' . $required . ' name="geox" id="c4g_brick_geopicker_geox" onchange="C4GGeopickerAddress(\'' . $profile_id . '\')" value="' . $lon . '" type="text" disabled="disabled" class="formdata" >' .
            '<input ' . $required . ' name="geoy" id="c4g_brick_geopicker_geoy" onchange="C4GGeopickerAddress(\'' . $profile_id . '\')" value="' . $lat . '" type="text" disabled="disabled" class="formdata" >' .
            $description .
            '</div><div id="c4g_brick_geopicker"></div>' .
            '<div id="c4g_brick_map"></div>';
        //}
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
        $db_geox = $dbValues->loc_geox;
        $db_geoy = $dbValues->loc_geoy;
        //$db_locstyle = $dbValues->locstyle;

        $dlg_geox = $dlgValues['geox'];
        $dlg_geoy = $dlgValues['geoy'];
        //$dlg_locstyle = $dlgValues[$fieldName];

        if ( ($db_geox != $dlg_geox) || ($db_geoy != $dlg_geoy)) {
            $result[] = new C4GBrickFieldCompare($this, $db_geox.'|'.$db_geoy, $dlg_geox.'|'.$dlg_geoy);
        }
    }
    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField(C4GContainer $rowData, $content)
    {
        // TODO: Implement getC4GListField() method.
    }

}