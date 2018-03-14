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

use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickRenderMode;
use con4gis\ProjectsBundle\Classes\Structures\C4GAbstractList;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GMoreButton extends C4GAbstractList
{
    private $renderModeOverride = '';
    //'entry' => Render each entry individually, preferably with font awesome icons.

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
        if ($this->getRenderModeOverride() !== '') {
           $renderMode = $this->getRenderModeOverride();
        }
        $class .= " ui-button ui-corner-all";
        $view = '';
        foreach ($this->entries as $key => $entry) {
            if ($entry instanceof C4GMoreButtonEntry) {

                //Block added to render entries based on conditions
                if ($entry instanceof C4GMoreButtonEntry) {
                    if ($entry->getCondition()) {
                        $continue = false;
                        foreach ($entry->getCondition() as $con) {
                            if (($con->getType() == C4GBrickConditionType::METHODSWITCH) && (!$con->checkAgainstCondition($dataId))) {
                                $continue = true;
                            }
                        }
                        if ($continue) {
                            continue;
                        }
                    }
                }

                if ($entry->getDynamicTitleCallback() && count($entry->getDynamicTitleCallback()) == 2) {
                    $callback = $entry->getDynamicTitleCallback();
                    $callClass = $callback[0];
                    $method = $callback[1];
                    $element = '<li style="display: none;" data-index="'. $key .'">' . $callClass::$method($dataId) . '</li>';
                    $title = $callClass::$method($dataId);
                    $entry->setTitle($title);
                } else {
                    $element = '<li style="display: none;" data-index="'. $key .'">' . $entry->getTitle() . '</li>';
                }
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
        if ($renderMode == 'entry') {
            $cssId = 'c4g_more_button_' . $fieldName . '_' . $dataId;
            $button = "<button id='" . $cssId . "' title='" . $title . "' style='display: none'>";
        } else {
            $cssId = 'c4g_more_button_' . $fieldName . '_' . $dataId;
            $button = "<button id='" . $cssId . "' class='" . $class . "' title='" . $title . "' role='button' onclick='" . $onclick . "'>";
        }
        $view .= $button;
        $view .= $title . '</button>';
        $view .= $this->renderContainer($renderMode, $fieldName, $dataId);
        return $view;
    }

    public function renderContainer($renderMode, $fieldName, $dataId)
    {
        if ($renderMode == 'entry') {
            $view = "<div><div class='c4g_more_button_container_hidden c4g_more_button_mode_$renderMode' id='c4g_more_button_" . $fieldName . "_" .
                $dataId . "_container' style='display: flex;'>";
        } elseif ($renderMode == 'tiles') {
            // we do not need the outer div in this rendermode
            $view = "<div class='c4g_more_button_container c4g_more_button_mode_$renderMode ui-widget-content ui-corner-all' id='c4g_more_button_" . $fieldName . "_" .
                $dataId . "_container' style='display: none;'>";
        } else {
            $view = "<div><div class='c4g_more_button_container c4g_more_button_mode_$renderMode ui-widget-content ui-corner-all' id='c4g_more_button_" . $fieldName . "_" .
                $dataId . "_container' style='display: none;'>";
        }
        $onclick = "executeSelection(this)";
        foreach ($this->entries as $key => $entry) {
            //Block added to render entries based on conditions
            if ($entry instanceof C4GMoreButtonEntry) {
                if ($entry->getCondition()) {
                    $continue = false;
                    foreach ($entry->getCondition() as $con) {
                        if (($con->getType() == C4GBrickConditionType::METHODSWITCH) && (!$con->checkAgainstCondition($dataId))) {
                            $continue = true;
                        }
                    }
                    if ($continue) {
                        continue;
                    }
                }
            }

            if ($renderMode == 'entry') {
                $element = "<span class='c4g_more_button_entry ui-button ui-corner-all'  href='morebutton_" . $fieldName . ":" . $dataId . ":" .
                    $key . "' onclick='" . $onclick . "'>";
            } else {
                $element = "<span class='c4g_more_button_entry ui-button'  href='morebutton_" . $fieldName . ":" . $dataId . ":" .
                    $key . "' onclick='" . $onclick . "'>";
            }
            $element .= $entry->getTitle();
            $element .= "</span>";
            $view .= $element;
        }
        if ($renderMode == 'tiles') {
            $view .= "</div>";
        } else {
            $view .= "</div></div>";
        }
        return $view;
    }

    /**
     * @return string
     */
    public function getRenderModeOverride()
    {
        return $this->renderModeOverride;
    }

    /**
     * @param string $renderModeOverride
     */
    public function setRenderModeOverride($renderModeOverride)
    {
        $this->renderModeOverride = $renderModeOverride;
    }

}