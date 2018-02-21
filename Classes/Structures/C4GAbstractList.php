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
 * Class C4GAbstractList
 * Abstract base class for creating dynamic lists.
 * @package con4gis\ProjectsBundle\Classes\Structures
 */
abstract class C4GAbstractList
{
    /**
     * @var C4GAbstractListEntry[]
     */
    protected $entries = [];

    /**
     * @return C4GAbstractListEntry[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * Adds an entry to the list.
     * @param C4GAbstractListEntry $entry   The entry to add.
     * @return int                          The index of the entry.
     */
    public function addEntry(C4GAbstractListEntry $entry)
    {
        $this->entries[] = $entry;
        return count($this->entries);
    }

    /**
     * Deletes the passed entry from the list, based on the equals method.
     * @param C4GAbstractListEntry $entry   The entry to delete.
     * @return C4GAbstractListEntry|bool    The removed entry or false, if the entry was not found.
     */
    public function deleteEntry(C4GAbstractListEntry $entry)
    {
        foreach ($this->entries as $key => $currentEntry) {
            if ($currentEntry->equals($entry)) {
                $return = $this->entries[$key];
                unset($this->entries[$key]);
                return $return;
            }
        }
        return false;
    }

    /**
     * Deletes the entry at the given index.
     * @param int $index                    The index of the entry to delete.
     * @return bool|C4GAbstractListEntry    The removed entry or false, if the index does not exist.
     */
    public function deleteEntryByIndex(int $index)
    {
        foreach ($this->entries as $key => $currentEntry) {
            if ($key == $index) {
                $return = $this->entries[$key];
                unset($this->entries[$key]);
                return $return;
            }
        }
        return false;
    }

    /**
     * Returns the entry at the given index.
     * @param int $index                    The index of the entry to get.
     * @return bool|C4GAbstractListEntry    The desired entry or false, if the index does not exist.
     */
    public function getEntryByIndex(int $index)
    {
        foreach ($this->entries as $key => $currentEntry) {
            if ($key == $index) {
                return $this->entries[$key];
            }
        }
        return false;
    }
}