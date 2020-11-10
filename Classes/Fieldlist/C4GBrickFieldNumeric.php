<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
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
        $this->setAlign('right');
    }

    public function createFieldData($dlgValues)
    {
        $fieldName = $this->getFieldName();
        $additionalId = $this->getAdditionalID();
        if (!empty($additionalId)) {
            $fieldName .= '_' . $additionalId;
        }
        $value = $dlgValues[$fieldName];

        if ($this->getThousandsSep() !== '') {
            $value = str_replace($this->getThousandsSep(), '', $dlgValues[$fieldName]);
        }

        if ($this->getDecimalPoint() === ',') {
            $value = str_replace($this->getDecimalPoint(), '.', $value);
            $value = (float) $value;
        } elseif ($this->getDecimalPoint() === '.') {
            $value = (float) $value;
        } else {
            $value = (int) $value;
        }

        if ($value > $this->max) {
            $value = $this->max;
        } elseif ($value < $this->min) {
            $value = $this->min;
        }

        $dlgValues[$fieldName] = $value;
        return $dlgValues[$fieldName];
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param $max
     * @return $this|C4GBrickField
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param $min
     * @return $this|C4GBrickField
     */
    public function setMin($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return int
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * @param $step
     * @return $this
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * @return string
     */
    public function getThousandsSep()
    {
        return $this->thousands_sep;
    }

    /**
     * @param $thousands_sep
     * @return $this
     */
    public function setThousandsSep($thousands_sep)
    {
        $this->thousands_sep = $thousands_sep;

        return $this;
    }

    /**
     * @return string
     */
    public function getDecimalPoint()
    {
        return $this->decimal_point;
    }

    /**
     * @param $decimal_point
     * @return $this
     */
    public function setDecimalPoint($decimal_point)
    {
        $this->decimal_point = $decimal_point;

        return $this;
    }

    /**
     * @return int
     */
    public function getDecimals()
    {
        return $this->decimals;
    }

    /**
     * @param $decimals
     * @return $this
     */
    public function setDecimals($decimals)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getRegEx()
    {
        return '';
    }
}
