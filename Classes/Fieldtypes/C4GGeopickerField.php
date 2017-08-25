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

namespace con4gis\ProjectBundle\Classes\Fieldtypes;

use con4gis\ProjectBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickFieldCompare;

class C4GGeopickerField extends C4GBrickField
{
    private $withoutAddressReloadButton = true; //do not show the address reload field
    private $withoutAddressRow = false; //do not show address row

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $title = $this->getTitle();
        $extModel = $this->getExternalModel();
        $extFieldName = $this->getExternalIdField();
        $latitudeField = $this->getLatitudeField();
        $longitudeField = $this->getLongitudeField();
        $addressField = $this->getAddressField();
        $withoutAddressReloadButton = $this->isWithoutAddressReloadButton();
        $withoutAddressRow = $this->isWithoutAddressRow();
        // $size = $field->getSize();
        $required = $this->generateRequiredString($data, $dialogParams);
        $database = $additionalParams['database'];
        $content = $additionalParams['content'];

        $address = null;
        if ($extModel && $extFieldName && $latitudeField && $longitudeField) {
            $lat = $data->$latitudeField;
            $lon = $data->$longitudeField;

            if ($addressField) {
                $address = $data->$addressField;
            }
        }
        else {
            $lon = $data->loc_geox;
            $lat = $data->loc_geoy;
        }

        $profile_id = null;
        if ($content) {
            $find = 'profile":"';
            $pos = strpos($content, $find);
            if ($pos > 0) {
                $str_profile_id = substr($content, $pos + 10, 4);
                if ($str_profile_id) {
                    $profile_id = intval($str_profile_id);
                }
            }
        }

        $address_line = '';
        if ($profile_id) {
            if (!$address || (strlen($address) <= 3)) {
                $address = C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database);
            } else {
                $address = $data->$addressField;
            }
        }
        $onChange = '';

        if(!$withoutAddressRow) {
            if ($withoutAddressReloadButton) {
                $onChange = 'onchange="C4GGeopickerAddress(\'' . $profile_id . '\')"';
                $address_line =
                    '<div class="c4g_reverse_address"><input name="c4g_geopicker_address" id="c4g_brick_geopicker_address" value="' . $address . '" type="text" disabled="disabled" class="formdata c4g_brick_geopicker_address_without_reloadbutton" ></div>';
            } else {
                $address_line =
                    '<div class="c4g_reverse_address"><input name="c4g_geopicker_address" id="c4g_brick_geopicker_address" value="' . $address . '" type="text" disabled="disabled" class="formdata" >' .
                    '<button id="c4g_addressUpdateButton" onClick="C4GGeopickerAddress(\'' . $profile_id . '\')">Adresse neu laden</button></div>';
            }
        }

        $condition = $this->createConditionData($fieldList, $data);
        $description = $this->getC4GDescriptionLabel($this->getDescription(),$condition);

        $result =
            $content . //Inhaltselement der Karte
            '<div '
            . $condition['conditionName']
            . $condition['conditionType']
            . $condition['conditionValue']
            . $condition['conditionDisable'] . '>'
            . $this->addC4GFieldLabel($id, $title, $this->isMandatory(),$condition,$fieldList,$data,$dialogParams)
            . $address_line.
            '<input ' . $required . ' name="geox" id="c4g_brick_geopicker_geox" '.$onChange.' value="' . $lon . '" type="text" disabled="disabled" class="formdata" >' .
            '<input ' . $required . ' name="geoy" id="c4g_brick_geopicker_geoy" value="' . $lat . '" type="text" disabled="disabled" class="formdata" >' .
            $description .
            '<div id="c4g_brick_geopicker"></div>' .
            '<div id="c4g_brick_map"></div></div>';


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
            return new C4GBrickFieldCompare($this, $db_geox.'|'.$db_geoy, $dlg_geox.'|'.$dlg_geoy);
        }
    }
    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField($rowData, $content, $database = null)
    {
        $lat = $rowData->loc_geoy;
        $lon = $rowData->loc_geox;
        $idFromModel = $rowData->id;
        $extModel = $this->getExternalModel();
        $profile_id = null;
        $withoutAddressRow = $this->isWithoutAddressRow();

        if ($content) {
            $find = 'profile":"';
            $pos = strpos($content, $find);
            if ($pos > 0) {
                $str_profile_id = substr($content, $pos + 10, 4);
                if ($str_profile_id) {
                    $profile_id = intval($str_profile_id);
                }
            }
        }


        $address = '';

        if (!$withoutAddressRow && $profile_id) {
            $addressField = $this->getAddressField();
            $address_db = $rowData->$addressField;
            if(!$address_db)
            {
                $extDbValues = $extModel::findByPk($idFromModel);
                $address = C4GBrickCommon::convert_coordinates_to_address($lat, $lon, $profile_id, $database);
                $extDbValues->$addressField = $address;
                $extDbValues->save();
            }
            else
            {
                $address = $address_db;
            }
        }
        return $address;
    }

    /**
     * @return bool
     */
    public function isWithoutAddressReloadButton()
    {
        return $this->withoutAddressReloadButton;
    }

    /**
     * @param bool $withoutAddressReloadButton
     */
    public function setWithoutAddressReloadButton($withoutAddressReloadButton)
    {
        $this->withoutAddressReloadButton = $withoutAddressReloadButton;
    }

    /**
     * @return bool
     */
    public function isWithoutAddressRow()
    {
        return $this->withoutAddressRow;
    }

    /**
     * @param bool $withoutAddressRow
     */
    public function setWithoutAddressRow($withoutAddressRow)
    {
        $this->withoutAddressRow = $withoutAddressRow;
    }


}