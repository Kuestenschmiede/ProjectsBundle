<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
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
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
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
            } elseif ($width) {
                $size = 'width="' . $width . '"';
            } elseif ($height) {
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
            $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);

            if ($path) {
//                if ($pathobj->path[0] != '/') {
//                    $pathobj->path = substr($pathobj->path, 1);
//                }
                if ($dialogParams->isWithLabels() === false) {
                    $label = '';
                } else {
                    $label = $this->getTitle();
                }

                if ($dialogParams->isWithDescriptions() === false) {
                    $description = '';
                }

                $img = "<img src=\"$path\" title=\"" . $this->getTitle() . "\" $size/>";
                $i = $this->getFieldName() . 'Link';
                $link = $data->$i;
                if ($link !== '') {
                    $img = '<a href="' . $link . "\" target=\"_blank\" rel=\"noopener noreferrer\">$img</a>";
                }

                $result = '
                        <div '
                    . $condition['conditionName']
                    . $condition['conditionType']
                    . $condition['conditionValue']
                    . $condition['conditionDisable'] . '>
                        <div class="c4g_image c4g_' . $this->getFieldName() . '"><div class="c4g_image_label"><label>' . $label . '</label></div><div class="c4g_image_src  c4g_' . $this->getFieldName() . '_src"></div><div class="c4g_image_description">' .
                    $img . $description . '</div></div></div>';
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
        $i = $this->getFieldName();
        if (strval($rowData->$i) === '') {
            return '';
        }

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
            } elseif ($width) {
                $size = 'width="' . $width . '"';
            } elseif ($height) {
                $size = 'height="' . $height . '"';
            }
        }

        $i = $this->getFieldName();

        try {
            $path = C4GBrickCommon::loadFile($rowData->$i)->path;
        } catch (\Throwable $throwable) {
            C4gLogModel::addLogEntry('projects', $throwable->getMessage());

            return '';
        }

        $result = '';

        if ($path) {
            $img = "<img src=\"$path\" title=\"" . $this->getTitle() . "\" $size/>";
            $i = $this->getFieldName() . 'Link';
            $link = $rowData->$i;
            if ($link !== '') {
                $result = '<a href="' . $link . "\" target=\"_blank\" rel=\"noopener noreferrer\" onclick=\"event.stopPropagation()\">$img</a>";
            }
        }

        return $result;
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
        if (!is_string($file)) {
            $file = '';
        }
        $fileObject = C4GBrickCommon::loadFile($file);
        if ($fileObject) {
            switch ($this->getFileTypes()) {
                case C4GBrickFileType::IMAGES_ALL:
                case C4GBrickFileType::IMAGES_JPG:
                case C4GBrickFileType::IMAGES_PNG:
                case C4GBrickFileType::IMAGES_PNG_JPG:
                    if ($fileObject->path[0] == '/') {
                        return $fieldTitle . '<div class="c4g_tile value">' . '<img src="' . substr($fileObject->path, 1) . '" width="' . $this->getSize() . '" height="' . $this->getSize() . '">' . '</div>';
                    }

                        return $fieldTitle . '<div class="c4g_tile value">' . '<img src="' . $fileObject->path . '" width="' . $this->getSize() . '" height="' . $this->getSize() . '">' . '</div>';

            }
        } else {
            switch ($this->getFileTypes()) {
                case C4GBrickFileType::IMAGES_ALL:
                case C4GBrickFileType::IMAGES_JPG:
                case C4GBrickFileType::IMAGES_PNG:
                case C4GBrickFileType::IMAGES_PNG_JPG:
                    return $fieldTitle . '<div class="c4g_tile value">' . '<img src="bundles/con4gisprojects/images/missing.svg">' . '</div>';

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
        }

        return '';
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
