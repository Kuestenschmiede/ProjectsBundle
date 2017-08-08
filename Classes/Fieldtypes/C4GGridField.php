<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Fieldtypes;

use con4gis\ProjectBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectBundle\Classes\Dialogs\C4GBrickGrid;
use con4gis\ProjectBundle\Classes\Fieldlist\C4GBrickField;

class C4GGridField extends C4GBrickField
{
    private $grid = null; //siehe C4GBrickGrid

    /**
     * C4GButtonField constructor.
     */
    public function __construct(C4GBrickGrid $grid)
    {
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
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $result = '';

        $grid = $this->grid;
        if  ($grid) {

            $condition = $this->createConditionData($fieldList, $data);

            $elements = '';
            $elementHTML = '';

            $rowCounter = 0;
            $tro = '';
            foreach ($grid->getElements() as $element) {

                $field  = $element->getField();

                if ($field) {
                    $col  = $element->getCol();
                    $row     = $element->getRow();
                    $colspan = $element->getColspan();
                    $rowspan = $element->getRowspan();
                    $horizontal = $element->getHorizontal();
                    $vertical = $element->getVertical();
                    $width   = $element->getWidth();

                    if ($row > $rowCounter) {
                      if ($row == 1) {
                          $tro = '<tr>';
                      }  else {
                          $tro = '</tr><tr>';
                      }
                      $rowCounter = $row;
                    } else {
                        $tro = '';
                    }

                    $tdRowspan = '';
                    if ($rowspan) {
                      $tdRowspan = ' rowspan="'.$rowspan.'" ';
                    }

                    $tdColspan = '';
                    if ($colspan) {
                        $tdColspan = ' colspan="'.$colspan.'" ';
                    }

                    $fieldHTML = $field->getC4GDialogField($fieldList, $data, $dialogParams);

                    $elementHTML .= $tro.'<td style="width:'.$width.';text-align:'.$horizontal.';vertical-align:'.$vertical.';padding-right: 2.0%;"'.$tdColspan.$tdRowspan.'><div class="c4g_brick_grid_box c4g_brick_grid_box_'.$col.'_'.$row.'">'.$fieldHTML.'</div></td>';
                }
            }

            $elementHTML .= '</tr>'; //close row

            //ToDo change table to display:grid if feature released for all standard browsers
            $html =
                '<table id="c4g_brick_grid '.$id.'" class="c4g_brick_grid_wrapper"><tbody>' .
                $elementHTML .
                '</tbody></table>';

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $html);
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
    }

    /**
     * @return C4GBrickGrid|null
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param C4GBrickGrid|null $grid
     */
    public function setGrid($grid)
    {
        $this->grid = $grid;
    }
}