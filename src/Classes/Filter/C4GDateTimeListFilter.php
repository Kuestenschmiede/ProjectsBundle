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
namespace con4gis\ProjectsBundle\Classes\Filter;

use con4gis\CoreBundle\Classes\Helper\ArrayHelper;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use Contao\Model\Collection;

class C4GDateTimeListFilter extends C4GListFilter
{
    protected int $from = 0;
    protected int $to = 0;
    protected string $fieldName = 'tstamp';
    protected string $buttonText = '';

    /**
     * @param $dlgValues
     * @param $brickKey
     */
    public function setFilter($dlgValues, $brickKey)
    {
        $this->from = strtotime($dlgValues['fromFilter']);
        $this->to = strtotime($dlgValues['toFilter']);
        $this->setFilterCookies($brickKey);
    }

    public function setDefaultFilter($from, $to)
    {
        if ($this->from === 0 && $this->to === 0) {
            $this->from = $from;
            $this->to = $to;
        }

        return $this;
    }

    /**
     * Filter out undesired elements and return the desired ones.
     * @param $elements
     * @param $dialogParams
     * @return mixed
     */
    public function filter($elements, $dialogParams)
    {
        if ($elements instanceof Collection) {
            $elements = $elements->fetchAll();
            $convertToObject = true;
        } else {
            $convertToObject = false;
        }
        if ($this->to && ($this->to >= $this->from)) {
            $fieldName = $this->fieldName;
            foreach ($elements as $key => $value) {
                if (is_array($value)) {
                    if ((intval($value[$this->fieldName]) < $this->from) || (intval($value[$this->fieldName]) > $this->to)) {
                        unset($elements[$key]);
                    }
                } else {
                    if ((intval($value->$fieldName) < $this->from) || (intval($value->$fieldName) > $this->to)) {
                        unset($elements->$key);
                    }
                }
            }
        }

        if ($convertToObject) {
            return ArrayHelper::arrayToObject($elements);
        }
        return $elements;
    }

    /**
     * Call listParams->addButton() to dynamically add the filter button to the list.
     * @param $listParams
     */
    public function addButton($listParams)
    {
        $listParams->addButton(C4GBrickConst::BUTTON_FILTER, $this->buttonText);
    }

    /**
     * @param $brickKey
     */
    protected function setFilterCookies($brickKey)
    {
        setcookie($brickKey . '_rangeFrom', date('d.m.Y', $this->from), time() + 3600, '/');
        setcookie($brickKey . '_rangeTo', date('d.m.Y', $this->to), time() + 3600, '/');
    }

    /**
     * @param $brickKey
     */
    public function getFilterCookies($brickKey)
    {
        $fromCookie = $_COOKIE[$brickKey . '_rangeFrom'];
        $toCookie = $_COOKIE[$brickKey . '_rangeTo'];
        $this->from = C4GBrickCommon::getTimeStampFromDate($fromCookie);
        $this->to = C4GBrickCommon::getTimeStampFromDate($toCookie);
    }

    /**
     * Return the text to be displayed above the table.
     *  Make sure to return different values for an active and an inactive filter, if appropriate.
     *  The return value may be an empty string.
     * @return mixed
     */
    public function getFilterHeadline(): string
    {
        if ($this->to && ($this->to >= $this->from)) {
            return sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['filterPeriod'], '<b>' . date('d.m.Y', $this->from) . '</b>', '<b>' . date('d.m.Y', $this->to) . '</b>');
        }

        return '';
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     * @return C4GDateTimeListFilter
     */
    public function setFieldName(string $fieldName): C4GDateTimeListFilter
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @param string $buttonText
     * @return C4GDateTimeListFilter
     */
    public function setButtonText(string $buttonText): C4GDateTimeListFilter
    {
        $this->buttonText = $buttonText;

        return $this;
    }
}
