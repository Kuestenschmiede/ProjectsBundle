<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Filter;

class C4GBrickFilterParams
{
    private $headtext = '';
    private $buttontext = '';
    private $minItems = 10; //ab diesem Wert stehen die Filterfelder zur Verfügung.

    private $withRangeFilter = false;
    private $withGeofilter = false;
    private $rangeFrom = ''; //Date, Timestamp, Woche, ...
    private $rangeTo = ''; //Date, Timestamp, Woche, ...

    private $withSelectFilter = false;
    private $withCheckboxFilter = false;
    private $withMethodFilter = false;
    private $dateTimeFilter = false;
    private $useMethodFilter = 0;
    private $filterMethod = [];

    private $fields = [];  // Checkbox = array('spaltenname' => 'Übersetzung', 'spaltenname' => 'Übersetzung');
    private $options = [];

    private $filterField = ''; // The fieldname of the field which the data should be filtered by
    private $withoutFiltertext = false;

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
     * @param $headtext
     * @return $this
     */
    public function setHeadtext($headtext)
    {
        $this->headtext = $headtext;

        return $this;
    }

    /**
     * @return string
     */
    public function getButtontext()
    {
        return $this->buttontext;
    }

    /**
     * @param $buttontext
     * @return $this
     */
    public function setButtontext($buttontext)
    {
        $this->buttontext = $buttontext;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinItems()
    {
        return $this->minItems;
    }

    /**
     * @param $minItems
     * @return $this
     */
    public function setMinItems($minItems)
    {
        $this->minItems = $minItems;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithRangeFilter()
    {
        return $this->withRangeFilter;
    }

    /**
     * @param bool $withRangeFilter
     * @return $this
     */
    public function setWithRangeFilter($withRangeFilter = true)
    {
        @trigger_error('Use of C4GBrickFilterParams is deprecated, use a C4GListFilter object instead.');
        $this->withRangeFilter = $withRangeFilter;

        return $this;
    }

    /**
     * @return string
     */
    public function getRangeFrom()
    {
        return $this->rangeFrom;
    }

    /**
     * @param $rangeFrom
     * @return $this
     */
    public function setRangeFrom($rangeFrom)
    {
        $this->rangeFrom = $rangeFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getRangeTo()
    {
        return $this->rangeTo;
    }

    /**
     * @param $rangeTo
     * @return $this
     */
    public function setRangeTo($rangeTo)
    {
        $this->rangeTo = $rangeTo;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithSelectFilter()
    {
        return $this->withSelectFilter;
    }

    /**
     * @param bool $withSelectFilter
     * @return $this
     */
    public function setWithSelectFilter($withSelectFilter = true)
    {
        @trigger_error('Use of C4GBrickFilterParams is deprecated, use a C4GListFilter object instead.');
        $this->withSelectFilter = $withSelectFilter;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithCheckboxFilter()
    {
        return $this->withCheckboxFilter;
    }

    /**
     * @param bool $withCheckboxFilter
     * @return $this
     */
    public function setWithCheckboxFilter($withCheckboxFilter = true)
    {
        @trigger_error('Use of C4GBrickFilterParams is deprecated, use a C4GListFilter object instead.');
        $this->withCheckboxFilter = $withCheckboxFilter;

        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    } // Werten wonach gesucht werden soll || $list[] = array('id' => 'der zu suchende Wert', 'name' => 'Name');

    /**
     * @return boolean
     */
    public function isWithGeofilter()
    {
        return $this->withGeofilter;
    }

    /**
     * @param $withGeofilter
     * @return $this
     */
    public function setWithGeofilter($withGeofilter = true)
    {
        @trigger_error('Use of C4GBrickFilterParams is deprecated, use a C4GListFilter object instead.');
        $this->withGeofilter = $withGeofilter;

        return $this;
    }

    /**
     * @return string
     */
    public function getFilterField()
    {
        return $this->filterField;
    }

    /**
     * @param $filterField
     * @return $this
     */
    public function setFilterField($filterField)
    {
        $this->filterField = $filterField;

        return $this;
    }

    public function setBrickFilterCookies($brickKey)
    {
        $fromValue = $this->rangeFrom;
        $toValue = $this->rangeTo;
        $useMethodFilter = $this->useMethodFilter;
        if ($fromValue && $toValue) {
            setcookie($brickKey . '_rangeFrom', $fromValue, time() + 3600, '/');
            setcookie($brickKey . '_rangeTo', $toValue, time() + 3600, '/');
        }
        if ($this->withMethodFilter) {
            setcookie($brickKey . '_methodFilter', $useMethodFilter, time() + 3600, '/');
        }

        return $this;
    }

    public function getBrickFilterCookies($brickKey)
    {
        $fromCookie = key_exists($brickKey . '_rangeFrom', $_COOKIE) ? $_COOKIE[$brickKey . '_rangeFrom'] : '';
        $toCookie = key_exists($brickKey . '_rangeTo', $_COOKIE) ? $_COOKIE[$brickKey . '_rangeTo'] : '';
        $methodFilterCookie = key_exists($brickKey . '_methodFilter', $_COOKIE) ? $_COOKIE[$brickKey . '_methodFilter'] : '';
        if ($fromCookie && $toCookie) {
            $this->rangeFrom = $fromCookie;
            $this->rangeTo = $toCookie;
        }
        if ($methodFilterCookie === 1 || $methodFilterCookie === '1') {
            $this->useMethodFilter = 1;
        } else {
            $this->useMethodFilter = 0;
        }
    }

    /**
     * @return bool
     */
    public function isWithoutFiltertext()
    {
        return $this->withoutFiltertext;
    }

    /**
     * @param bool $withoutFiltertext
     */
    public function setWithoutFiltertext(bool $withoutFiltertext)
    {
        $this->withoutFiltertext = $withoutFiltertext;
    }

    /**
     * @return bool
     */
    public function isWithMethodFilter(): bool
    {
        return $this->withMethodFilter;
    }

    /**
     * @param bool $withMethodFilter
     * @return C4GBrickFilterParams
     */
    public function setWithMethodFilter(bool $withMethodFilter = true): C4GBrickFilterParams
    {
        @trigger_error('Use of C4GBrickFilterParams is deprecated, use a C4GListFilter object instead.');
        $this->withMethodFilter = $withMethodFilter;

        return $this;
    }

    /**
     * @return array
     */
    public function getFilterMethod(): array
    {
        return $this->filterMethod;
    }

    /**
     * @param array $filterMethod
     * @return $this
     */
    public function setFilterMethod(array $filterMethod)
    {
        $this->filterMethod = $filterMethod;

        return $this;
    }

    /**
     * @return int
     */
    public function getUseMethodFilter(): int
    {
        return $this->useMethodFilter;
    }

    public function toggleMethodFilter()
    {
        if (($this->useMethodFilter === 1) || ($this->useMethodFilter === '1')) {
            $this->useMethodFilter = 0;
        } else {
            $this->useMethodFilter = 1;
        }
    }

    /**
     * @return bool
     */
    public function isDateTimeFilter(): bool
    {
        return $this->dateTimeFilter;
    }

    /**
     * @param bool $dateTimeFilter
     */
    public function setDateTimeFilter(bool $dateTimeFilter): void
    {
        $this->dateTimeFilter = $dateTimeFilter;
    }
}
