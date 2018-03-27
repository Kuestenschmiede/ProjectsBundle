<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Fieldlist;


abstract class C4GBrickFieldText extends C4GBrickField
{
    /**
     * Properties
     * @property string $pattern Regular expression this field's value must meet.
     */
    //Todo Alle nötigen Properties aus BrickField und den Kindern hier einfügen
    //Todo Prüfen, ob alles funktioniert und erst danach die Properties aus BrickField und den Kindern löschen.


    protected $pattern = '';

    /**
     * Will be called by ShowListAction if the field value is longer than the column width. Return a value that will replace it.
     * This will not overwrite the value stored in the database.
     * @param $value
     * @param $columnWidth
     * @return string
     */

    public function cutFieldValue($value, $columnWidth)
    {
        return substr($value, 0, $columnWidth - 3) . '...';
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

}