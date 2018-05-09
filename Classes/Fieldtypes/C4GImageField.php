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

use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldSourceType;
use con4gis\ProjectsBundle\Classes\Files\C4GBrickFileType;

class C4GImageField extends C4GBrickField
{
    private $width = '256px';
    private $height = '256px';
    private $deserialize = false;

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $size = $this->getSize();
        if ($size) {
            $width = $size;
            $height = $size;
            $size = 'width="' . $width . '" height="' . $height . '"';

        } else {
            $width = $this->getWidth();
            $height = $this->getHeight();

            if ($width && $height) {
                $size = 'width="' . $width . '" height="' . $height . '"';
            } else if ($width) {
                $size = 'width="' . $width . '"';
            } else if ($height) {
                $size = 'height="' . $height . '"';
            }
        }
        //If the Image-Field has another field as source, load the image from that field instead
        if ($this->getSource() == C4GBrickFieldSourceType::OTHER_FIELD) {
            $sourceFieldName = $this->getSourceField();
            $sourceField = null;
            foreach ($fieldList as $arrField) {
                if ($arrField->getFieldName() == $sourceFieldName) {
                    $sourceField = $arrField;
                    break;
                }
            }
            if ($sourceField) {
                $value = $sourceField->generateInitialValue($data);
            } else {
                //TODO fehlerbehandlung
                $value = '';
            }

        } else {
            $value = $this->generateInitialValue($data);
        }

        if ($this->deserialize) {
            if ($value) {
                $path = deserialize($value)[0];
                //$path = \Contao\FilesModel::findOneBy('path', $value);
            }
        } else {
            $pathobj = C4GBrickCommon::loadFile($value);
            $path = $pathobj->path;

        }

        $result = '';

        if ($this->isShowIfEmpty() || $path) {

            $condition = $this->createConditionData($fieldList, $data);
            $description =$this->getC4GDescriptionLabel($this->getDescription(), $condition);

            if ($path) {
//                if ($pathobj->path[0] != '/') {
//                    $pathobj->path = substr($pathobj->path, 1);
//                }

                $result = '
                        <div '
                    . $condition['conditionName']
                    . $condition['conditionType']
                    . $condition['conditionValue']
                    . $condition['conditionDisable'] . '>
                        <div class="c4g_image c4g_' . $this->getFieldName() . '"><div class="c4g_image_label"><label>' . $this->getTitle() . '</label></div><div class="c4g_image_src  c4g_' . $this->getFieldName() . '_src"><img src="' . $path . '" title="' . $this->getTitle() .
                    '" '.$size.'/></div><div class="c4g_image_description">' .
                    $description . '</div></div></div>';
            }
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
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeserialize()
    {
        return $this->deserialize;
    }

    /**
     * @param $deserialize
     * @return $this
     */
    public function setDeserialize($deserialize)
    {
        $this->deserialize = $deserialize;
        return $this;
    }

}