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
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GGalleryField extends C4GBrickField
{
    private $imageWidth  = '';
    private $imageHeight = '';
    private $withTitle   = false;
    private $maxImages   = 0; //0 show all

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {

        //set image sizes
        $size = $this->getSize();
        if ($size) {
            $width = $size;
            $height = $size;
            $size = 'width: ' . $width .';'. ' height: ' . $height . ';';

        } else {
            $width = $this->getImageWidth();
            $height = $this->getImageHeight();

            if ($width && $height) {
                $size = 'width: ' . $width . ';'.'height: ' . $height . ';';
            } else if ($width) {
                $size = 'width: ' . $width . ';';
            } else if ($height) {
                $size = 'height: ' . $height . ';';
            }
        }
        if ($size) {
            $size = 'style="'.$size.'"';
        }

        $value = $this->generateInitialValue($data);
        $files = deserialize($value);

        $result = '';

        if ($this->isShowIfEmpty() || $files) {

            $condition = $this->createConditionData($fieldList, $data);
            $description =$this->getC4GDescriptionLabel($this->getDescription(), $condition);

            if ($files) {
                $images = '';
                $image_cnt = 0;
                foreach ($files as $file) {
                    $imageFile = C4GBrickCommon::loadFile($file);
                    $src = $imageFile->path;
                    $title = $this->withTitle ? $imageFile->name : '';
                    $image_cnt++;
                    $images .= '<li class="ce_image c4g_gallery_image c4g_' . $this->getFieldName() .'_'.$image_cnt.' block"><figure class="image_container" itemscope="" itemtype="http://schema.org/ImageObject"><a href="' . $src . '" data-lightbox="c4g_' . $this->getFieldName() .'_'.$image_cnt.'" data-title="' . $title . '"><img src="' . $src . '" itemprop="image" title="' . $title . '" '.$size.'/></a></figure></li>';

                    if ($image_cnt >= $this->maxImages) {
                        break;
                    }
                }

                $result = '<div '
                    . $condition['conditionName']
                    . $condition['conditionType']
                    . $condition['conditionValue']
                    . $condition['conditionDisable'] . '>
                        <div class="c4g_gallery formdata c4g_' . $this->getFieldName() . '"><div class="c4g_image_label"><label>' . $this->getTitle() . '</label></div><ul class="c4g_gallery_images c4g_' . $this->getFieldName() . '_images">'.$images.'</ul><div class="c4g_gallery_description">' .
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
//    /**
//     * Public method for creating the field specific list HTML
//     * @param $rowData
//     * @param $content
//     * @return mixed
//     */
//    public function getC4GListField($rowData, $content)
//    {
//        $fieldName = $this->getFieldName();
//        $file = $rowData->$fieldName;
//        if (!is_string($file)) {
//            $file = '';
//        }
//
//        $fileObject = C4GBrickCommon::loadFile($file);
//        if ($fileObject) {
//            $field = $fileObject->name;
//        } else {
//            $field = 'UNKNOWN';
//        }
//        return $field;
//    }
//
//    /**
//     * Public method for creating the field specific tile HTML
//     * @param $fieldTitle
//     * @param $element
//     * @return mixed
//     */
//    public function getC4GTileField($fieldTitle, $element)
//    {
//        $fieldName = $this->getFieldName();
//        $file = $element->$fieldName;
//        if (!is_string($file))
//        {
//            $file = '';
//        }
//        $fileObject = C4GBrickCommon::loadFile($file);
//        if ($fileObject) {
//            switch($this->getFileTypes())
//            {
//                case C4GBrickFileType::IMAGES_ALL:
//                case C4GBrickFileType::IMAGES_JPG:
//                case C4GBrickFileType::IMAGES_PNG:
//                case C4GBrickFileType::IMAGES_PNG_JPG:
//                    if($fileObject->path[0] == '/')
//                    {
//                        return $fieldTitle . '<div class="c4g_tile value">' . '<img src="' .substr ($fileObject->path, 1 ). '" width="'.$this->getSize().'" height="'.$this->getSize().'">' . '</div>';
//                    }
//                    else
//                    {
//                        return $fieldTitle . '<div class="c4g_tile value">' . '<img src="' .$fileObject->path. '" width="'.$this->getSize().'" height="'.$this->getSize().'">' . '</div>';
//                    }
//            }
//        }
//        else
//        {
//            switch($this->getFileTypes())
//            {
//                case C4GBrickFileType::IMAGES_ALL:
//                case C4GBrickFileType::IMAGES_JPG:
//                case C4GBrickFileType::IMAGES_PNG:
//                case C4GBrickFileType::IMAGES_PNG_JPG:
//                    return $fieldTitle . '<div class="c4g_tile value">' . '<img src="bundles/con4gisprojects/images/missing.png">' . '</div>';
//                    break;
//                default:
//                    return $fieldTitle . '<div class="c4g_tile value">' . '<div class="error"></div>' . '</div>';
//            }
//        }
//    }
//
//    /**
//     * Public method that will be called in translateFieldValues in C4GBrickModuleParent
//     * @param $value
//     * @return mixed
//     */
//    public function translateFieldValue($value)
//    {
//        $file = $value;
//        if (!is_string($file)) {
//            $file = '';
//        }
//
//        $fileObject = C4GBrickCommon::loadFile($file);
//        if ($fileObject) {
//            return $fileObject->name;
//        } else {
//            return 'UNKNOWN';
//        }
//    }

    /**
     * @return string
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * @param $imageWidth
     * @return $this
     */
    public function setImageWidth($imageWidth)
    {
        $this->imageWidth = $imageWidth;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * @param $imageHeight
     * @return $this
     */
    public function setImageHeight($imageHeight)
    {
        $this->imageHeight = $imageHeight;
        return $this;
    }

    /**
     * @return bool
     */
    public function isWithTitle()
    {
        return $this->withTitle;
    }

    /**
     * @param $withTitle
     * @return $this
     */
    public function setWithTitle($withTitle)
    {
        $this->withTitle = $withTitle;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxImages()
    {
        return $this->maxImages;
    }

    /**
     * @param $maxImages
     * @return $this
     */
    public function setMaxImages($maxImages)
    {
        $this->maxImages = $maxImages;
        return $this;
    }
}