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


use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GDataTableField extends C4GBrickField
{
    private $usesEditor = true;         //Enhance the Datatable using Editor

    /*
        hidden input field that holds the ids as csv
        use events in the js to add or remove data to or from it
        write jQuery parameters that are needed to initialise the table into the html
        add a js to the module to create the table(s)
     */

    public function getTableContent() {

    }





    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $inptId = 'c4g_'.$this->getFieldname();
        $tableId = 'c4g_datatable_'.$this->getFieldName();
        $fieldName = $this->getFieldName();
        $pattern = '';

        $html = '<div class="c4g_datatable_container">';
        $html .= "<input id='$inptId' type='text' name='$fieldName' readonly='true' pattern='$pattern'>";
        $html .= "<table id='$tableId' class='c4g_datatable'>"
            . "<thead>"
            . "<tr><th></th><th></th></tr>"
            . "</thead>"
            . "<tbody>"
            . "<tr><td></td><td></td>"
            . "</tr><tr><td></td><td></td></tr>"
            . "</tbody>"
            . "</table>";
        $html .= '</div>';
    }

    public function compareWithDB($dbValue, $dlgvalue)
    {

    }

}