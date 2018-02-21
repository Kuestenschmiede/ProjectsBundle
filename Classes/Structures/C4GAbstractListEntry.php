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

namespace con4gis\ProjectsBundle\Classes\Structures;

/**
 * Class C4GAbstractListEntry
 * Abstract base class to extend for list elements.
 * @package con4gis\ProjectsBundle\Classes\Structures
 */
abstract class C4GAbstractListEntry
{
    /**
     * Compare method for list entries. Must be overriden in inheriting classes.
     * @param C4GAbstractListEntry $entry   The entry to compare to this.
     * @return bool                         True when the entries should be treated equal, false otherwise.
     */
    public abstract function equals(C4GAbstractListEntry $entry);
}