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

namespace con4gis\ProjectsBundle\Classes\Buttons;

use con4gis\ProjectsBundle\Classes\Lists\C4GBrickRenderMode;
use con4gis\ProjectsBundle\Classes\Structures\C4GAbstractList;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

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

    public function renderButton($class, $title, $fieldName, $renderMode, $dataId)
    {
        $class .= " ui-button ui-corner-all";
        $view = '';
        foreach ($this->entries as $key => $entry) {
            if ($entry instanceof C4GMoreButtonEntry) {
                $element = '<li style="display: none;" data-index="'. $key .'">' . $entry->getTitle() . '</li>';
                $view .= $element;
            }
        }
        if (count($this->entries) == 1) {
            // TODO hänge die click action direkt an den button (später)
            $onclick = 'toggleContainer(this)';
        } else {
            // TODO renderContainer aufrufen und den container in die view hängen
            $onclick = 'toggleContainer(this)';

        }
        $cssId = 'c4g_more_button_' . $fieldName . '_' . $dataId;
        $button = "<button id='" . $cssId . "' class='" . $class . "' title='" . $title . "' role='button' onclick='" . $onclick . "'>";
        $view .= $button;
        $view .= $title . '</button>';
        $view .= $this->renderContainer($renderMode, $fieldName, $dataId);
        return $view;
    }

    public function renderContainer($renderMode, $fieldName, $dataId)
    {
        $view = "<div class='c4g_more_button_container' id='c4g_more_button_" . $fieldName . "_" .
            $dataId . "_container' style='visibility: hidden;'>";
        $onclick = "executeSelection(this)";
        foreach ($this->entries as $key => $entry) {
            $element = "<span class='c4g_more_button_entry' href='morebutton_" . $fieldName . ":" . $dataId . ":" .
                $key . "' onclick='" . $onclick . "'>";
            $element .= $entry->getTitle();
            $element .= "</span>";
            $view .= $element;
        }
        $view .= "</div>";
        return $view;
    }
}