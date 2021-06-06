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
use con4gis\ProjectsBundle\Classes\Signature\SignatureToImage;

class C4GSignaturePadField extends C4GBrickField
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
        if (empty(trim($value))) {
            if ($this->isEditable()) {
                $script = '<script>jQuery(document).ready(function() {jQuery(".c4g_brick_dialog").signaturePad({drawOnly:true});});</script>';
            } else {
                $script = '<script>jQuery(document).ready(function() {jQuery(".c4g_brick_dialog").signaturePad({displayOnly:true});});</script>';
            }
        } else {
            $value = str_replace('&quot;', '"', $value);
            if ($this->isEditable()) {
                $script = '<script>jQuery(document).ready(function() {jQuery(".c4g_brick_dialog").signaturePad({drawOnly:true}).regenerate('.$value.');});</script>';
            } else {
                $script = '<script>jQuery(document).ready(function() {jQuery(".c4g_brick_dialog").signaturePad({displayOnly:true}).regenerate('.$value.');});</script>';
            }
        }

        if ($this->isShowIfEmpty() || !empty(trim($value))) {
            $fieldData = '<div class="c4g-signature-pad ui-corner-all">'.
                    '<ul class="sigNav">'.
                      '<li class="clearButton"><a href="#clear">'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['DELETE'].'</a></li>'.
                    '</ul>'.
                    '<div class="sig sigWrapper">'.
                      '<div class="typed"></div>'.
                      '<canvas class="pad" width="198" height="55" autofocus></canvas>'.
                      '<input id="' . $id . '" type="hidden" name="' .$this->getFieldName() . '" class="formdata output c4g-signature-pad-output' .
                      $this->getFieldName() . '" value="'.$value.'">'.
                    '</div>'.$script;

            $condition = $this->createConditionData($fieldList, $data);

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $fieldData);
        }

        return $result;
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return array|string
     */
    public function getC4GPrintField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $fieldName = $this->getFieldName();
        $result = '';

        if ($data && $data->$fieldName) {
            $value = $data->$fieldName;
            $value = str_replace('&quot;', '"', $value);
            $img = SignatureToImage::sigJsonToImage($value, array('imageSize'=>array(198, 55)));
            $fileName = tempnam(sys_get_temp_dir(), '/c4g/sig_png_' . rand(0, 543435) . '.png');
            $type = pathinfo($fileName, PATHINFO_EXTENSION);
            imagepng($img, $fileName);
            imagedestroy($img);
            $data = file_get_contents($fileName);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $result = '<img src="'.$base64.'" alt="" width="198" height="55">';
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

    /**
     * @param $dlgValues
     * @return array|string|string[]
     */
    public function createFieldData($dlgValues)
    {
        $fieldName = $this->getFieldName();
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldName .= '_' . $additionalId;
        }

        return $dlgValues[$fieldName];
    }
}
