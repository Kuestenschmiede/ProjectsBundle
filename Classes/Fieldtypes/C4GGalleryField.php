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

class C4GGalleryField extends C4GBrickField
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
        $title = $this->getTitle();
        $values = $this->getInitialFields();
        $link = $this->getInsertLink() . '?state=brickdialog:';

        $action = $this->getAction();
        
        //ToDo new implementation
        //$event = new C4GBrickAction($action['actiontype'], $action['actionevent']);
        $event = null;

        if (!$values) {
            $loadOptions = $this->getLoadOptions();
            if ($loadOptions) {
                $model = $loadOptions->getModel();
                $keyField = $loadOptions->getKeyField();
                $idField = $loadOptions->getIdField();
                $nameField = $loadOptions->getNameField();
                $pathField = $loadOptions->getPathField();
                $publishedField = $loadOptions->getPublishedField();


                $id = $data->id;
                $elements = $model::findby($keyField, $id);

                if ($elements) {
                    foreach ($elements as $element) {
                        if (($publishedField == '') ||
                            ($element->$publishedField)
                        ) {
                            $initialField = new C4GImageField();
                            if ($element->$nameField) {

                                $initialField->setTitle($element->$nameField);
                            }
                            if ($this->getSize()) {
                                $initialField->setSize($this->getSize());
                            }
                            if ($element->$pathField) {

                                $initialField->setInitialValue($element->$pathField);
                            }
                            if ($element->$idField) {
                                $initialField->setContentId($element->$idField);
                            }
                            $values[] = $initialField;
                        }
                    }
                }
            }
        }

        $width = $this->getSize();


        $result = '';

        if ($this->isShowIfEmpty() || !empty($values)) {

            $condition = $this->createConditionData($fieldList, $data);
            $description = $this->getC4GDescriptionLabel($this->getDescription(), $condition);

            $result = '<div '
                . $condition['conditionName']
                . $condition['conditionType']
                . $condition['conditionValue']
                . $condition['conditionDisable'] . '>
                <label>' . $title . '</label><div class="c4g_gallery">';

            foreach ($values as $value) {
                // $values are the initialFields (C4GBrickFields)

                $path = $value->generateInitialValue($data);

                $contentId = $value->getContentId();

                $pathobj = C4GBrickCommon::loadFile($path);
                if ($this->isShowIfEmpty() || !empty($pathobj->path)) {
                    if ($pathobj->path) {

                        if ($pathobj->path[0] == '/') {
                            $result .= '
                                <div class="c4g_tile">
                                        <div class="c4g_tile_label">
                                        <label>' . $value->getTitle() . '</label>
                                        </div>
                                        <div class="c4g_tile_image">
                                            <a ' . $event->showResult()['js'] . ' data-mfp-src="' . $link . $contentId . '" href="' . substr($pathobj->path, 1) . '" class="' . $event->showResult()['class'] . '">
                                                <img src="' . substr($pathobj->path, 1) . '" title="" width="' . $width . '"/>
                                            </a>
                                        </div>
                                        <div class="c4g_tile_description">' . $description . '</div>

                                </div>';
                        } else {
                            $result .= '
                                <div class="c4g_tile">
                                        <div class="c4g_tile_label">
                                        <label>' . $value->getTitle() . '</label>
                                        </div>
                                        <div class="c4g_tile_image">
                                            <a ' . $event->showResult()['js'] . ' data-mfp-src="' . $link . $contentId . '" href="' . $pathobj->path . '" class="' . $event->showResult()['class'] . '">
                                                <img src="' . $pathobj->path . '" title="" width="' . $width . '"/>
                                            </a>
                                        </div>
                                        <div class="c4g_tile_description">' . $description . '</div>
                                </div>';
                        }
                    } else {
                        $result = '
                        <div class="c4g_tile_label"><label>' .
                            $title . '</label></div><div class="c4g_tile_image"><img src="system/modules/con4gis_projects/assets/missing.png" title="' .
                            $title . '" width="' . $width . '"/></div><div class="c4g_tile_description">' . $description . '</div></div>';
                    }
                }

            }

            $result .= '</div></div> ' . $event->showResult()['script'];
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