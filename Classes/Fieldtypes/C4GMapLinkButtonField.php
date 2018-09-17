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

class C4GMapLinkButtonField extends C4GLinkButtonField
{
    private $latitudeColumn = '';       //Database column that has the latitude value.
    private $longitudeColumn = '';      //Database column that has the longitude value
    private $zoom = 16;                 //Map zoom level
    private $baseLayer = 1;             //Map base layer

    public function __construct()
    {
        $this->setDatabaseField(false)
            ->setFormField(false)
            ->setTableColumn()
            ->setDatabaseField(false);
    }

    /**
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
        return array();
    }

    protected function createHref($rowData, $content) {
        $latField = $this->latitudeColumn;
        $longField = $this->longitudeColumn;
        $lat = $rowData->$latField;
        $lon = $rowData->$longField;
        $zoom = $this->zoom;
        $baseLayer = $this->baseLayer;
        $html = parent::createHref($rowData, $content);
        return $html . "#$lon/$lat/$zoom/0/$baseLayer/0";
    }

    /**
     * @return string
     */
    public function getLatitudeColumn(): string
    {
        return $this->latitudeColumn;
    }

    /**
     * @param string $latitudeColumn
     * @return C4GLinkButtonField
     */
    public function setLatitudeColumn(string $latitudeColumn): C4GLinkButtonField
    {
        $this->latitudeColumn = $latitudeColumn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongitudeColumn(): string
    {
        return $this->longitudeColumn;
    }

    /**
     * @param string $longitudeColumn
     * @return C4GLinkButtonField
     */
    public function setLongitudeColumn(string $longitudeColumn): C4GLinkButtonField
    {
        $this->longitudeColumn = $longitudeColumn;
        return $this;
    }

    /**
     * @return int
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * @param int $zoom
     * @return C4GLinkButtonField
     */
    public function setZoom(int $zoom): C4GLinkButtonField
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * @return int
     */
    public function getBaseLayer(): int
    {
        return $this->baseLayer;
    }

    /**
     * @param int $baseLayer
     * @return C4GMapLinkButtonField
     */
    public function setBaseLayer(int $baseLayer): C4GMapLinkButtonField
    {
        $this->baseLayer = $baseLayer;
        return $this;
    }



}