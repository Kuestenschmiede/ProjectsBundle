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

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use Contao\Database;

class C4GGeopickerField extends C4GBrickField
{
    private $withoutAddressReloadButton = true; //do not show the address reload field
    private $withoutAddressRow = false; //do not show address row
    private $locGeoxFieldname = 'loc_geox';
    private $locGeoyFieldname = 'loc_geoy';

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
        // if there is already an address set, use that
        $fieldName = $this->getFieldName();
        if ($rowData->$fieldName) {
            return $rowData->$fieldName;
        }
        $lat = $rowData->loc_geoy;
        $lon = $rowData->loc_geox;
        $idFromModel = $rowData->id;
        $extModel = $this->getExternalModel();
        $profile_id = null;
        $withoutAddressRow = $this->isWithoutAddressRow();
        $db = Database::getInstance();
        $settings = $db->execute("SELECT * FROM tl_c4g_settings LIMIT 1")->fetchAssoc();
        if ($settings['defaultprofile']) {
            $profile_id = $settings['defaultprofile'];
        } else {
            // old way of getting profile id; kept for backwards compatibility
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
     * Returns false if the field is not mandatory or if it is mandatory but its conditions are not met.
     * Otherwise it checks whether the field has a valid value and returns the result.
     * @param array $dlgValues
     * @return bool|C4GBrickField
     */

    public function checkMandatory($dlgValues)
    {
        //$this->setSpecialMandatoryMessage($this->getFieldName()); //Useful for debugging
        if (!$this->isMandatory()) {
            return false;
        } elseif(!$this->isDisplay()) {
            return false;
        } elseif ($this->getCondition()) {
            foreach ($this->getCondition() as $con) {
                $fieldName = $con->getFieldName();
                if (!$con->checkAgainstCondition($dlgValues[$fieldName])) {
                    return false;
                }
            }
        }
        $loc_geox = $dlgValues['geox'];
        $loc_geoy = $dlgValues['geoy'];
        if ($loc_geox && $loc_geoy) {
            return true;
        } else {
            return $this;
        }
    }

    /**
     * @return bool
     */
    public function isWithoutAddressReloadButton()
    {
        return $this->withoutAddressReloadButton;
    }

    /**
     * @param $withoutAddressReloadButton
     * @return $this
     */
    public function setWithoutAddressReloadButton($withoutAddressReloadButton)
    {
        $this->withoutAddressReloadButton = $withoutAddressReloadButton;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithoutAddressRow()
    {
        return $this->withoutAddressRow;
    }

    /**
     * @param $withoutAddressRow
     * @return $this
     */
    public function setWithoutAddressRow($withoutAddressRow)
    {
        $this->withoutAddressRow = $withoutAddressRow;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocGeoxFieldname()
    {
        return $this->locGeoxFieldname;
    }

    /**
     * @param string $locGeoxFieldname
     * @return C4GGeopickerField
     */
    public function setLocGeoxFieldname($locGeoxFieldname)
    {
        $this->locGeoxFieldname = $locGeoxFieldname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocGeoyFieldname()
    {
        return $this->locGeoyFieldname;
    }

    /**
     * @param string $locGeoyFieldname
     * @return C4GGeopickerField
     */
    public function setLocGeoyFieldname($locGeoyFieldname)
    {
        $this->locGeoyFieldname = $locGeoyFieldname;
        return $this;
    }

}