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

    const DIGITS = '^[0-9](\d*)$'; //Whole numbers, from 0 to infinite, no negatives and no separators.
    const DIGITS_NEG = '^[-]?[0-9](\d*)$'; //Digits, but allowed to go negative.
    const NUMBERS = '^[1-9](\d*)$'; //Whole numbers, from 1 to infinite, no negatives and no separators.
    const NUMBERS_NO_SEP = '^[-]?[1-9](\d*)$';  //Whole numbers, not allowing thousand separators.
    const NUMBERS_DOT_SEP = '^[-]?[1-9](\d*)((.)(\d{3}))*$';  //Whole numbers, allowing dots as thousand separators.
    const HEX_DEC_ID = '^[0-9A-Fa-f]*$'; //Hexadecimal IDs, e.g. 00A51B.
    const NUMBERS_COMMA_SEP = '[-]?[1-9](\d*)((,)(\d{3}))*$';  //Whole numbers, allowing commas as thousand separators.
    const POSTAL = '^[0-9]{5}$';  //Zip Codes
    const EMAIL = '[^ @]*@[^ .@]*\.[^ .@\d]{2,6}$'; //EMail Addresses
    const PHONE = '^\+?[\d\s]{3,}$'; //Phone numbers
    const NAME = '^[\p{L}]{1}[- \p{L}]*[\p{L}]{1}$'; //Names, allows special letters (ä, á, etc.) as well as whitespaces and (-) unless they are at the end or beginning.
    const URL = '^(https?:\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)$'; //URL
    const YEARS = '^[1-2][90](\d{2})$'; //Years, from 1900 to 20xx. Will also allow 29xx, so make sure to set a max value!


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
        if ($allowNegative === true) {
            $allowNegative = '[-]?';
        } else {
            $allowNegative = '';
        }

        if ($decimals <= 0) {
            $decimals = 0;
            $decimalPoint = '';
        }
        return '^'. $allowNegative .'(\d+)(('. $thousandsSep .')(\d{3}))*((('. $decimalPoint .'))(\d{0,'. $decimals .'}))?$';
    }
}