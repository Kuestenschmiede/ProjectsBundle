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

namespace con4gis\ProjectsBundle\Classes\Buttons;

use con4gis\ProjectsBundle\Classes\Structures\C4GAbstractList;

class C4GMoreButton extends C4GAbstractList
{
    public function deleteEntryByTitle(string $title)
    {
        foreach ($this->entries as $key => $currentEntry) {
            if ($currentEntry instanceof C4GMoreButtonEntry && $currentEntry->getTitle() === $title) {
                $return = $this->entries[$key];
                unset($this->entries[$key]);
                return $return;
            }
        }
    }

    public function getEntryByTitle(string $title)
    {
        foreach ($this->entries as $key => $currentEntry) {
            if ($currentEntry instanceof C4GMoreButtonEntry && $currentEntry->getTitle() === $title) {
                return $this->entries[$key];
            }
        }
    }

    public function renderButton($class, $title, $fieldName)
    {
        $button = "<button class='" . $class . "' title='Mehr...' role='button' onclick='showOptions(this,\"". $fieldName ."\")'>";
        $view = $button;
        foreach ($this->entries as $key => $entry) {
            if ($entry instanceof C4GMoreButtonEntry) {
                $element = '<li style="display: none;" data-index="'. $key .'">' . $entry->getTitle() . '</li>';
                $view .= $element;
            }
        }
        $view .= $title . '</button>';
        return $view;
    }
}