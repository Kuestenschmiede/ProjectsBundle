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

use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Files\C4GBrickFileType;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GFileField extends C4GBrickField
{
    private $maxFileSize = '4194304';
    private $nameFormat   = ''; //standardmäßig wird eine eindeutiger Name generiert (uuid)
    private $withDate     = false;
    private $withNumber   = false;

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $homeDir = $dialogParams->getHomeDir();
        $viewType = $dialogParams->getViewType();
        if (!$homeDir) {
            return '';
        }

        $fieldName = $this->getFieldName();
        $fileTypes = $this->getFileTypes();

        $id = "c4g_" . $fieldName;
        $title = $this->getTitle();
        $required = $this->generateRequiredString($data, $dialogParams);
        $buttonRequired = $required;
        if ((!$this->isEditable() ||
            ($viewType && (
                    ($viewType == C4GBrickViewType::PUBLICVIEW) ||
                    ($viewType == C4GBrickViewType::GROUPVIEW) ||
                    ($viewType == C4GBrickViewType::PROJECTPARENTVIEW) ||
                    ($viewType == C4GBrickViewType::MEMBERVIEW) )
            ))
        ) {
            $buttonRequired = 'disabled readonly style="display:none"';
        }
        $value = $this->generateInitialValue($data);

        $file = $data->$fieldName;
        if (!is_string($file)) {
            $file = '';
        }

        $fileObject = C4GBrickCommon::loadFile($file);

        $targetField = '';
        foreach ($fieldList as $arrField) {
            $sourceField = $arrField->getSourceField();
            if ($sourceField && ($sourceField == $fieldName)) {
                $targetField = $arrField->getFieldName();
                break;
            }
        }

        $file_link = '<label id="c4g_uploadLink"></label>' .
            '<button id="c4g_deleteButton" ' . $buttonRequired . ' onClick="deleteC4GBrickFile(\'' . $targetField . '\')" style="display:none"></button>';

        if ($fileObject) {
            $file_uuid  = $fileObject->uuid;
            $file_url   = $fileObject->path;
            $file_label = basename($fileObject->name);
            if ($file_label) {
                $file_label = str_replace("C:\\fakepath\\", "", $file_label);
            }
            $file_link =
                '<label id="c4g_uploadLink"><a href="' . $file_url . '" target="_blank">' . $file_label . '</a>' .
                '<button id="c4g_deleteButton" ' . $buttonRequired . ' onClick="deleteC4GBrickFile(\'' . $targetField . '\')"></button></label>';
        }


        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<button id="c4g_uploadButton" ' . $buttonRequired . ' ' . $condition['conditionPrepare'] . ' onClick="document.getElementById(\'' . $id . '\').click()">'.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['FILE_UPLOAD'].'</button>' .
                $file_link . C4GHTMLFactory::lineBreak() .
                '<input type="hidden" id="c4g_uploadURL" name="c4g_uploadURL" class="formdata" ' . $condition['conditionPrepare'] . ' value="' . $file_url . '">' .
                '<input type="hidden" id="c4g_deleteURL" name="c4g_deleteURL" class="formdata" ' . $condition['conditionPrepare'] . ' value="">' .
                '<input type="file" id="' . $id . '"  class="formdata ' . $id . '" ' . $condition['conditionPrepare'] . ' name="' . $fieldName . '"' .
                ' multiple="false" accept="' . $fileTypes . '" maxlength="'.$this->maxFileSize.'"' .
                'onchange="handleC4GBrickFile(this.files,\'' . $homeDir . '\',\'' . $targetField . '\',\'' . $fileTypes . '\');" value="' . $file_url . '" ' . $required . ' style="display:none">');
        }

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
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $fileObject = C4GBrickCommon::loadFile($dbValue);
        if ($fileObject) {
            $file_url = $fileObject->path;
        }

        $url = $dlgValues['c4g_uploadURL'];
        if (strcmp($url, $file_url) != 0) {
            $result[] = new C4GBrickFieldCompare($this, $file_url, $url);
        }
    }

    /**
     * Method that will be called in the saveC4GDialog() in C4GBrickDialog
     * @return array
     */
    public function createFieldData($dlgValues, $dbValues = null)
    {
        $fieldData = $dlgValues[$this->getFieldName()];
        $original_filename = $fieldData;
        $fieldName = $this->getFieldName();
        $upload_url = $dlgValues['c4g_uploadURL'];
        $old_file = $dbValues->$fieldName;

        $fileObject = C4GBrickCommon::loadFile($old_file);
        if ($fileObject) {
          $old_url = $fileObject->path;
        }

        if (!empty($upload_url) && (!\Validator::isUuid($upload_url)) && (strcmp($upload_url, $old_url) != 0)) {
            $new_upload_url = $upload_url;

            $ext = pathinfo($upload_url, PATHINFO_EXTENSION);
            $dir = pathinfo($upload_url, PATHINFO_DIRNAME);
            if ($this->nameFormat) {
                $fields = explode('-', $this->nameFormat);
                $fileName = '';
                $first = true;
                foreach ($fields as $field) {
                    if ($first) {
                        $fileName = $dlgValues[$field];
                        $first = false;
                    } else {
                        $fileName .= '-'.$dlgValues[$field];
                    }
                }

                if ($fileName) {
                    $fileName = preg_replace("([^\w\s\d\.\-_~,;:\[\]\(\)]|[\.]{2,})", '', $fileName);
                }
            }
            if ($this->isWithDate()) {
                $date = date('d-m-Y');
                if ($fileName) {
                    $fileName = $fileName . '-' . $date;
                }
            }
            if ($this->isWithNumber()) {
                if ($fileName) {
                    $i=1;
                    $tmpName = $fileName.'-'.str_pad($i, 3 ,'0', STR_PAD_LEFT);
                    while (file_exists($_SERVER["DOCUMENT_ROOT"].$dir . '/' . $tmpName . '.' . $ext)) {
                        $i=$i+1;
                        $tmpName = $fileName.'-'.str_pad($i, 3 ,'0', STR_PAD_LEFT);
                    }
                    $fileName = $tmpName;
                }
            }

            if ($fileName) {
                $fileName = $fileName . '.' . $ext;
                $new_upload_url = $dir . '/' . $fileName;
            }

            $fieldData = C4GBrickCommon::saveFile($fieldName, $original_filename, $new_upload_url, $upload_url);
        }

        $delete_file = $dlgValues['c4g_deleteURL'];

        if (!empty($delete_file) && ($fieldData != $old_file)) {

            if (!empty($old_file) && (!\Validator::isUuid($old_file)) ) {
                C4GBrickCommon::deleteFile($delete_file);
            } else if (!empty($old_file)) {
                C4GBrickCommon::deleteFileByUUID($old_file);
            }

            $fieldData = "";
        } else {
            if (!\Validator::isUuid($fieldData)) {
                $fieldData = $old_file;
            }
        }
        return $fieldData;
    }

    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField($rowData, $content)
    {
        $fieldName = $this->getFieldName();
        $file = $rowData->$fieldName;
        if (!is_string($file)) {
            $file = '';
        }

        $fileObject = C4GBrickCommon::loadFile($file);
        if ($fileObject) {
            $field = $fileObject->name;
        } else {
            $field = 'UNKNOWN';
        }
        return $field;
    }

    /**
     * Public method for creating the field specific tile HTML
     * @param $fieldTitle
     * @param $element
     * @return mixed
     */
    public function getC4GTileField($fieldTitle, $element)
    {
        $fieldName = $this->getFieldName();
        $file = $element->$fieldName;
        if (!is_string($file))
        {
            $file = '';
        }
        $fileObject = C4GBrickCommon::loadFile($file);
        if ($fileObject) {
            switch($this->getFileTypes())
            {
                case C4GBrickFileType::IMAGES_ALL:
                case C4GBrickFileType::IMAGES_JPG:
                case C4GBrickFileType::IMAGES_PNG:
                case C4GBrickFileType::IMAGES_PNG_JPG:
                case C4GBrickFileType::IMAGES_PNG_JPG_TIFF:
                    if($fileObject->path[0] == '/')
                    {
                        return $fieldTitle . '<div class="c4g_tile value">' . '<img src="' .substr ($fileObject->path, 1 ). '" width="'.$this->getSize().'" height="'.$this->getSize().'">' . '</div>';
                    }
                    else
                    {
                        return $fieldTitle . '<div class="c4g_tile value">' . '<img src="' .$fileObject->path. '" width="'.$this->getSize().'" height="'.$this->getSize().'">' . '</div>';
                    }
            }
        }
        else
        {
            switch($this->getFileTypes())
            {
                case C4GBrickFileType::IMAGES_ALL:
                case C4GBrickFileType::IMAGES_JPG:
                case C4GBrickFileType::IMAGES_PNG:
                case C4GBrickFileType::IMAGES_PNG_JPG:
                case C4GBrickFileType::IMAGES_PNG_JPG_TIFF:
                   return $fieldTitle . '<div class="c4g_tile value">' . '<img src="bundles/con4gisprojects/images/missing.png">' . '</div>';
                    break;
                default:
                   return $fieldTitle . '<div class="c4g_tile value">' . '<div class="error"></div>' . '</div>';
            }
        }
    }

    /**
     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
     * @param $value
     * @return mixed
     */
    public function translateFieldValue($value)
    {
        $file = $value;
        if (!is_string($file)) {
            $file = '';
        }

        $fileObject = C4GBrickCommon::loadFile($file);
        if ($fileObject) {
            return $fileObject->name;
        } else {
            return 'UNKNOWN';
        }
    }

    /**
     * @return string
     */
    public function getMaxFileSize()
    {
        return $this->maxFileSize;
    }

    /**
     * @param string $maxFileSize
     */
    public function setMaxFileSize($maxFileSize)
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * @return string
     */
    public function getNameFormat()
    {
        return $this->nameFormat;
    }

    /**
     * @param string $nameFormat
     */
    public function setNameFormat($nameFormat)
    {
        $this->nameFormat = $nameFormat;
    }

    /**
     * @return boolean
     */
    public function isWithDate()
    {
        return $this->withDate;
    }

    /**
     * @param boolean $withDate
     */
    public function setWithDate($withDate)
    {
        $this->withDate = $withDate;
    }

    /**
     * @return boolean
     */
    public function isWithNumber()
    {
        return $this->withNumber;
    }

    /**
     * @param boolean $withNumber
     */
    public function setWithNumber($withNumber)
    {
        $this->withNumber = $withNumber;
    }
}