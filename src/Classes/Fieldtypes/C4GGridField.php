<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickGrid;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GGridField extends C4GBrickField
{
    private $grid = null; //siehe C4GBrickGrid

    /**
     * @param string $type
     */
    public function __construct(C4GBrickGrid $grid, string $type = C4GBrickFieldType::GRID)
    {
        parent::__construct($type);
        $this->setDatabaseField(false);
        $this->setComparable(false);

        $this->grid = $grid;
    }
    
    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $result = '';

        $grid = $this->grid;
        if ($grid) {
            $condition = $this->createConditionData($fieldList, $data);
            $elements = '';
            $elementHTML = '';


            //css grid
            if ($grid->getColumns()) {
                $col = $grid->getColumns();
                foreach ($grid->getElements() as $element) {
                    $field = $element->getField();

                    if ($field) {
                        $fieldHTML = $field->getC4GDialogField($fieldList, $data, $dialogParams);

                        $elementHTML .= '<div class="c4g__form-grid-element">' . $fieldHTML . '</div>';
                    }
                }

                $html =
                    '<div id="c4g__form-grid-' . $this->getFieldName() . '" class="c4g__form-grid-'. $col . '">' .
                    $elementHTML.'</div>';
            } else { //table grid - deprecated
                $rowCounter = 0;
                $tro = '';
                foreach ($grid->getElements() as $element) {
                    $field = $element->getField();

                    if ($field) {
                        $col = $element->getCol();
                        $row = $element->getRow();
                        $colspan = $element->getColspan();
                        $rowspan = $element->getRowspan();
                        $horizontal = $element->getHorizontal();
                        $vertical = $element->getVertical();
                        $width = $element->getWidth();

                        if ($row > $rowCounter) {
                            if ($row == 1) {
                                $tro = '<tr>';
                            } else {
                                $tro = '</tr><tr>';
                            }
                            $rowCounter = $row;
                        } else {
                            $tro = '';
                        }

                        $tdRowspan = '';
                        if ($rowspan) {
                            $tdRowspan = ' rowspan="' . $rowspan . '" ';
                        }

                        $tdColspan = '';
                        if ($colspan) {
                            $tdColspan = ' colspan="' . $colspan . '" ';
                        }

                        $fieldHTML = $field->getC4GDialogField($fieldList, $data, $dialogParams);

                        $elementHTML .= $tro . '<td style="width:' . $width . ';text-align:' . $horizontal . ';vertical-align:' . $vertical . ';padding-right: 2.0%;"' . $tdColspan . $tdRowspan . '><div class="c4g_brick_grid_box c4g_brick_grid_box_' . $col . '_' . $row . '">' . $fieldHTML . '</div></td>';
                    }
                }

                $elementHTML .= '</tr>'; //close row

                //ToDo change table to display:grid if feature released for all standard browsers
                $html =
                    '<table id="c4g_brick_grid ' . $id . '" class="c4g_brick_grid_wrapper"><tbody>' .
                    $elementHTML .
                    '</tbody></table>';
            }

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $html);
        }

        return $result;
    }

    public function getC4GListField($rowData, $content)
    {
        $elementHTML = '';
        if ($this->getGrid()) {
            $grid = $this->getGrid();
            if ($grid->getColumns()) {
                $col = $grid->getColumns();
                $count = 0;
                foreach ($grid->getElements() as $gridElement) {
                    $count++;
                    $field = $gridElement->getField();

                    if ($field) {
                        $fieldHTML = $field->getC4GTileField($field->getFieldName(), $rowData);
                        $fieldHTML = '<div class="c4g__list-list-element c4g_list-grid-element--' . $this->getFieldName() . '">' . $fieldHTML . '</div>';
                        $elementHTML .= $count > 1 ? ' ' . $fieldHTML : $fieldHTML;
                    }
                }

                $html =
                    '<div id="c4g__list-grid-' . $this->getFieldName() . '" class="c4g__list-grid-' . $col . ' c4g__list-grid-' . $this->getFieldName() . '">' .
                    $elementHTML . '</div>';
            }
        }

        return $html;
    }

    /**
     * @param $fieldTitle
     * @param $element
     * @param $column
     * @param $fieldList
     * @param C4GBrickDialogParams $dialogParams
     * @return string
     *
     * for css grid
     */
    public function getC4GTileField($fieldTitle, $element)
    {
        $elementHTML = '';
        if ($this->getGrid()) {
            $grid = $this->getGrid();
            if ($grid->getColumns()) {
                $col = $grid->getColumns();
                $count = 0;
                foreach ($grid->getElements() as $gridElement) {
                    $count++;
                    $field = $gridElement->getField();

                    if ($field) {

                        $fieldHTML = $field->getC4GTileField($field->getFieldName(), $element);
                        $fieldHTML = '<div class="c4g__tile-grid-element c4g_tile-grid-element--' . $this->getFieldName() . '">'.$fieldHTML.'</div>';
                        $elementHTML .= $count > 1 ? ' '.$fieldHTML : $fieldHTML;
                    }
                }

                $html =
                    '<div id="c4g__tile-grid-' . $this->getFieldName() . '" class="c4g__tile-grid-' . $col . ' c4g__tile-grid-' . $this->getFieldName() . '">' .
                    $elementHTML . '</div>';
            }
        }

        return $html;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
    }

    /**
     * @return C4GBrickGrid|null
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param $grid
     * @return $this
     */
    public function setGrid($grid)
    {
        $this->grid = $grid;

        return $this;
    }
}
