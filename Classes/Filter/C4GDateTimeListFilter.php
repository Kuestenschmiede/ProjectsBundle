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
namespace con4gis\ProjectsBundle\Classes\Filter;


use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;

class C4GDateTimeListFilter extends C4GListFilter
{
    protected $from = 0;
    protected $to = 0;
    protected $fieldName = 'tstamp';
    protected $buttonText = '';

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

    /**
     * Filter out undesired elements and return the desired ones.
     * @param $elements
     * @param $dialogParams
     * @return mixed
     */
    public function filter($elements, $dialogParams)
    {
        if ($this->to && (intval($this->to) >= intval($this->from))) {
            foreach ($elements as $key => $value) {
                if (is_array($value)) {
                    if ((intval($value[$this->fieldName]) < intval($this->from)) || (intval($value[$this->fieldName]) > intval($this->to))) {
                        unset($elements[$key]);
                    }
                } else {
                    $fieldName = $this->fieldName;
                    if ((intval($value->$fieldName) < intval($this->from)) || (intval($value->$fieldName) > intval($this->to))) {
                        unset($elements->$key);
                    }
                }
            }
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
        setcookie($brickKey.'_rangeFrom', date('d.m.Y', $this->from), time()+3600, '/');
        setcookie($brickKey.'_rangeTo', date('d.m.Y', $this->to), time()+3600, '/');
    }

    /**
     * @param $brickKey
     */
    public function getFilterCookies($brickKey)
    {
        $fromCookie = $_COOKIE[$brickKey.'_rangeFrom'];
        $toCookie = $_COOKIE[$brickKey.'_rangeTo'];
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
        if ($this->to && (intval($this->to) >= intval($this->from))) {
            return sprintf($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['filterPeriod'], '<b>'.date('d.m.Y', $this->from).'</b>', '<b>'.date('d.m.Y', $this->to.'</b>'));
        } else {
            return '';
        }
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