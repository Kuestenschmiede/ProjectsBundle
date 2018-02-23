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
namespace con4gis\ProjectsBundle\Classes\Filter;

class C4GBrickFilterParams
{
    private $headtext = '';
    private $buttontext = '';
    private $minItems = 10; //ab diesem Wert stehen die Filterfelder zur Verfügung.

    private $withRangeFilter = false;
    private $withGeofilter = false;
    private $rangeFrom  = ''; //Date, Timestamp, Woche, ...
    private $rangeTo    = ''; //Date, Timestamp, Woche, ...

    private $withSelectFilter = false;
    private $withCheckboxFilter = false;

    private $fields = array();  // Checkbox = array('spaltenname' => 'Übersetzung', 'spaltenname' => 'Übersetzung');
    private $options = array();

    private $filterField = ''; // The fieldname of the field which the data should be filtered by

    /**
     * C4GBrickFilterParams constructor.
     */
    public function __construct($brickKey)
    {
        $this->getBrickFilterCookies($brickKey);
    }


    /**
     * @return string
     */
    public function getHeadtext()
    {
        return $this->headtext;
    }

    /**
     * @param string $headtext
     */
    public function setHeadtext($headtext)
    {
        $this->headtext = $headtext;
    }

    /**
     * @return string
     */
    public function getButtontext()
    {
        return $this->buttontext;
    }

    /**
     * @param string $buttontext
     */
    public function setButtontext($buttontext)
    {
        $this->buttontext = $buttontext;
    }

    /**
     * @return int
     */
    public function getMinItems()
    {
        return $this->minItems;
    }

    /**
     * @param int $minItems
     */
    public function setMinItems($minItems)
    {
        $this->minItems = $minItems;
    }

    /**
     * @return boolean
     */
    public function isWithRangeFilter()
    {
        return $this->withRangeFilter;
    }

    /**
     * @param boolean $withRangeFilter
     */
    public function setWithRangeFilter($withRangeFilter)
    {
        $this->withRangeFilter = $withRangeFilter;
    }

    /**
     * @return string
     */
    public function getRangeFrom()
    {
        return $this->rangeFrom;
    }

    /**
     * @param string $rangeFrom
     */
    public function setRangeFrom($rangeFrom)
    {
        $this->rangeFrom = $rangeFrom;
    }

    /**
     * @return string
     */
    public function getRangeTo()
    {
        return $this->rangeTo;
    }

    /**
     * @param string $rangeTo
     */
    public function setRangeTo($rangeTo)
    {
        $this->rangeTo = $rangeTo;
    }

    /**
     * @return boolean
     */
    public function isWithSelectFilter()
    {
        return $this->withSelectFilter;
    }

    /**
     * @param boolean $withSelectFilter
     */
    public function setWithSelectFilter($withSelectFilter)
    {
        $this->withSelectFilter = $withSelectFilter;
    }

    /**
     * @return boolean
     */
    public function isWithCheckboxFilter()
    {
        return $this->withCheckboxFilter;
    }

    /**
     * @param boolean $withCheckboxFilter
     */
    public function setWithCheckboxFilter($withCheckboxFilter)
    {
        $this->withCheckboxFilter = $withCheckboxFilter;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    } // Werten wonach gesucht werden soll || $list[] = array('id' => 'der zu suchende Wert', 'name' => 'Name');

    /**
     * @return boolean
     */
    public function isWithGeofilter()
    {
        return $this->withGeofilter;
    }

    /**
     * @param boolean $withGeofilter
     */
    public function setWithGeofilter($withGeofilter)
    {
        $this->withGeofilter = $withGeofilter;
    }

    /**
     * @return string
     */
    public function getFilterField()
    {
        return $this->filterField;
    }

    /**
     * @param string $filterField
     */
    public function setFilterField($filterField)
    {
        $this->filterField = $filterField;
    }

    public function setBrickFilterCookies($brickKey) {
        $fromValue = $this->rangeFrom;
        $toValue = $this->rangeTo;
        if($fromValue && $toValue) {
            setcookie($brickKey.'_rangeFrom', $fromValue, time()+3600, '/');
            setcookie($brickKey.'_rangeTo', $toValue, time()+3600, '/');
        }
    }

    public function getBrickFilterCookies($brickKey) {
        $fromCookie = $_COOKIE[$brickKey.'_rangeFrom'];
        $toCookie   = $_COOKIE[$brickKey.'_rangeTo'];
        if ($fromCookie && $toCookie) {
            $this->rangeFrom = $fromCookie;
            $this->rangeTo = $toCookie;
        }
    }
}