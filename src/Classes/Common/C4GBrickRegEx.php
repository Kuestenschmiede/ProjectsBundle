<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Common;

/**
 * Class C4GBrickRegEx
 * Used to store and generate commonly used regular expressions.
 * @package con4gis\ProjectsBundle\Classes\Common
 */
class C4GBrickRegEx
{
    /**
     * Constant Expressions
     */
    const DIGITS = '^[0-9](\d*)$'; //Whole numbers, from 0 to infinite, no negatives and no separators.
    const DIGITS_NEG = '^[-]?[0-9](\d*)$'; //Digits, but allowed to go negative.
    const NUMBERS = '^[1-9](\d*)$'; //Whole numbers, from 1 to infinite, no negatives and no separators.
    const NUMBERS_NO_SEP = '^[-]?[0-9](\d*)$';  //Whole numbers, not allowing thousand separators.
    const NUMBERS_DOT_SEP = '^[-]?[1-9](\d*)((.)(\d{3}))*$';  //Whole numbers, allowing dots as thousand separators.
    const HEX_DEC_ID = '^[0-9A-Fa-f]*$'; //Hexadecimal IDs, e.g. 00A51B.
    const NUMBERS_COMMA_SEP = '[-]?[1-9](\d*)((,)(\d{3}))*$';  //Whole numbers, allowing commas as thousand separators.
    const POSTAL = '^[0-9]{4,5}$';  //Zip Codes 4 numbers for swiss
    const EMAIL = '[^ @]*@[^ .@]*\.[^ .@\d]{2,6}$'; //EMail Addresses
    const PHONE = '^\+?[\d\s]{3,}$'; //Phone numbers
    const NAME = '^[\p{L}]{1}[- \p{L}]*[\p{L}]{1}$'; //Names, allows special letters (ä, á, etc.) as well as whitespaces and (-) unless they are at the end or beginning.
    const URL = '^(https?:\/\/)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)$'; //URL
    const YEARS = '^[1][90](\d{2})|[2][0](\d{2})$'; //Years, from 1900 to 20xx. Will also allow 29xx, so make sure to set a max value!
    const DATE_D_M_Y = '^([0-2][0-9]|[3][0-1])[.]([0][0-9]|[1][0-2])[.]([1][90](\d{2})|[2][0](\d{2}))$'; //Dates in DD.MM.YYYY format
    const DATE_Y_M_D = '(?:19|20)(?:[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:29|30))|(?:(?:0[13578]|1[02])-31))|(?:[13579][26]|[02468][048])-02-29)'; //Dates in YYYY-MM-DD format

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

        return '^' . $allowNegative . '(\d+)((' . $thousandsSep . ')(\d{3}))*(((' . $decimalPoint . '))(\d{0,' . $decimals . '}))?$';
    }
}
