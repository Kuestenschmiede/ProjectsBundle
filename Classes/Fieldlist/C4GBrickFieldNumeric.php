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

namespace con4gis\ProjectsBundle\Classes\Fieldlist;


abstract class C4GBrickFieldNumeric extends C4GBrickField
{
    /**
     * Properties
     * @property integer $max Maxmimum value. Default: 99,999
     * @property integer $min Minimum value. Default: 0
     * @property integer $step Interval for steps. Default: 1
     * @property string $thousands_sep Thousands Separator. Default: empty
     * @property string $decimal_point Decimal point for fields with decimals. Default: ,
     * @property integer $decimals Number of decimals. Default: 0
     *
     */
    //Todo Alle nötigen Properties aus BrickField und den Kindern hier einfügen
    //Todo Prüfen, ob alles funktioniert und erst danach die Properties aus BrickField und den Kindern löschen.


    protected $max = 99999;
    protected $min = 0;
    protected $step = 1;
    protected $thousands_sep = '';
    protected $decimal_point = ',';
    protected $decimals = 0;
    protected $pattern = '';

    /**
     * C4GBrickFieldNumeric constructor.
     */
    public function __construct()
    {
        $this->setAlign("right");
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param int $step
     */
    public function setStep($step)
    {
        $this->step = $step;
    }

    /**
     * @return string
     */
    public function getThousandsSep()
    {
        return $this->thousands_sep;
    }

    /**
     * @param string $thousands_sep
     */
    public function setThousandsSep($thousands_sep)
    {
        $this->thousands_sep = $thousands_sep;
    }

    /**
     * @return string
     */
    public function getDecimalPoint()
    {
        return $this->decimal_point;
    }

    /**
     * @param string $decimal_point
     */
    public function setDecimalPoint($decimal_point)
    {
        $this->decimal_point = $decimal_point;
    }

    /**
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param int $decimals
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
    }

    public function getRegEx()
    {
        return '';
    }

}