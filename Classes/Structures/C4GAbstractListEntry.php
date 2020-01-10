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
    abstract public function equals(C4GAbstractListEntry $entry);
}
