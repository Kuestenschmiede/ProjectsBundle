<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Buttons;

use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickConditionType;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickRenderMode;
use con4gis\ProjectsBundle\Classes\Structures\C4GAbstractList;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

class C4GMoreButton extends C4GAbstractList
{
    private $renderModeOverride = '';
    const RENDER_MODE_ENTRY = 'entry'; //render each entry individually  in table view
    const RENDER_MODE_ENTRY_TILES = 'entry_tiles'; //render each entry individually in tile view


    private $toolTip = '';

    public function deleteEntryByTitle($title)
    {
        foreach ($this->entries as $key => $currentEntry) {
            if ($currentEntry instanceof C4GMoreButtonEntry && $currentEntry->getTitle() === $title) {
                $return = $this->entries[$key];
                unset($this->entries[$key]);
                return $return;
            }
        }
    }

    public function getEntryByTitle($title)
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
        if ($renderMode == self::RENDER_MODE_ENTRY || $renderMode == self::RENDER_MODE_ENTRY_TILES) {
            $cssId = 'c4g_more_button_' . $fieldName . '_' . $dataId;
            $button = "<button id='" . $cssId . "' title='" . $this->toolTip . "' style='display: none'>";
        } else {
            $cssId = 'c4g_more_button_' . $fieldName . '_' . $dataId;
            $button = "<button id='" . $cssId . "' class='" . $class . "' title='" . $this->toolTip . "' role='button' onclick='" . $onclick . "'>";
        }
        $view .= $button;
        $view .= $title . '</button>';
        $view .= $this->renderContainer($renderMode, $fieldName, $dataId);
        return $view;
    }

    public function renderContainer($renderMode, $fieldName, $dataId)
    {
        switch($renderMode) {
            case self::RENDER_MODE_ENTRY:
                $view = "<div><div class='c4g_more_button_container_hidden c4g_more_button_mode_$renderMode' id='c4g_more_button_" . $fieldName . "_" .
                    $dataId . "_container' style='display: flex;'>";
                break;
            case C4GBrickRenderMode::TILEBASED:
                // we do not need the outer div in this rendermode
                $view = "<div class='c4g_more_button_container c4g_more_button_mode_$renderMode ui-widget-content ui-corner-all' id='c4g_more_button_" . $fieldName . "_" .
                    $dataId . "_container' style='display: none;'>";
                break;
            case self::RENDER_MODE_ENTRY_TILES:
                $view = "<div class='c4g_more_button_container_hidden c4g_more_button_mode_$renderMode' id='c4g_more_button_" . $fieldName . "_" .
                    $dataId . "_container' style='display: inline-block;'>";
                break;
            default:
                $view = "<div><div class='c4g_more_button_container c4g_more_button_mode_$renderMode ui-widget-content ui-corner-all' id='c4g_more_button_" . $fieldName . "_" .
                    $dataId . "_container' style='display: none;'>";
                break;
        }

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
            if ($entry->getCallMode() == C4GMoreButtonEntry::CALLMODE_JS) {
                $callable = $entry->getCallable();
                $onclick = $callable . '(' . $dataId;
                $onclick .= ');';
            } else {
                $onclick = "executeSelection(this, event)";
            }



            if ($renderMode == self::RENDER_MODE_ENTRY || $renderMode == self::RENDER_MODE_ENTRY_TILES) {
                $tooltip = 'title="' . $entry->getToolTip() . '"';
                $element = "<span class='c4g_more_button_". $renderMode ." ui-button ui-corner-all'  href='morebutton_" . $fieldName . ":" . $dataId . ":" .
                    $key . "' onclick='" . $onclick . "' $tooltip>";
            } else {
                $element = "<span class='c4g_more_button_entry ui-button'  href='morebutton_" . $fieldName . ":" . $dataId . ":" .
                    $key . "' onclick='" . $onclick . "'>";
            }
            $element .= $entry->getTitle();
            $element .= "</span>";
            $view .= $element;
        }
        if ($renderMode == C4GBrickRenderMode::TILEBASED || $renderMode == self::RENDER_MODE_ENTRY_TILES) {
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
     * @param $renderModeOverride
     * @return $this
     */
    public function setRenderModeOverride($renderModeOverride)
    {
        $this->renderModeOverride = $renderModeOverride;
        return $this;
    }

    /**
     * @return string
     */
    public function getToolTip()
    {
        return $this->toolTip;
    }

    /**
     * @param $toolTip
     * @return $this
     */
    public function setToolTip($toolTip)
    {
        $this->toolTip = $toolTip;
        return $this;
    }

}