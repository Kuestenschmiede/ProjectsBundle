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

namespace con4gis\ProjectsBundle\Classes\Columns;


class C4GTextColumn extends C4GDataTableFieldColumn
{
    private $pattern = '';

    public function validateFieldValue($value)
    {

    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return C4GTextColumn
     */
    public function setPattern(string $pattern): C4GTextColumn
    {
        $this->pattern = $pattern;
        return $this;
    }



}