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

namespace con4gis\ProjectsBundle\Classes\Common;


/**
 * Class C4GBrickRegEx
 * Used to store and generate commonly used regular expressions.
 * @package con4gis\ProjectsBundle\Classes\Common
 */
class C4GBrickRegEx
{
    //Todo Prüfen, welche Regulären Ausdrücke wirklich nützlich sind und aufgenommen werden sollten.
    //Todo Dabei darauf achten, welche dynamisch (function) und welche konstant (const) sein sollen.


    /**
     * Constant Expressions
     */


    const NUMBERS_NO_SEP = '^[+-]?[1-9](\d+)';  //Whole numbers, not allowing thousand separators.
    const NUMBERS_DOT_SEP = '[+-]?[1-9](\d+)((.)(\d{3}))*';  //Whole numbers, allowing dots as thousand separators.
    const NUMBERS_COMMA_SEP = '[+-]?[1-9](\d+)((,)(\d{3}))*';  //Whole numbers, allowing commas as thousand separators.
    const POSTAL = '[0-9]{5}';  //Postal Codes


    /**
     * Dynamic Expressions
     * Dynamically generate a regular expressions based on given parameters.
     */

    /**
     * @param int $decimals         How many decimals are allowed. Use 0 for natural/whole numbers. Default: 2
     * @param bool $allowNegative   Whether negative numbers are allowed. Default: true
     * @param string $thousandsSep  What to use as the thousand separator. Default: .
     * @param string $decimalPoint  What to use as decimal point. Default: ,
     * @return string expression    The generated regular expression.
     */
    public static function generateNumericRegEx($decimals = 2, $allowNegative = true, $thousandsSep = '.', $decimalPoint = ',')
    {
        if ($allowNegative == true) {
            $allowNegative = '[+-]?';
        }

        if ($decimals <= 0) {
            $decimals = 0;
            $decimalPoint = '';
        }
        return '^'. $allowNegative .'[1-9](\d+)(('. $thousandsSep .')(\d{3}))*((('. $decimalPoint .'))(\d{0,'. $decimals .'}))?';
    }
}