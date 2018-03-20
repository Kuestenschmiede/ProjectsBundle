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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

/**
 * Class C4GHeadlineField
 * @package con4gis\ProjectsBundle\Classes\Fieldtypes
 */
class C4GHeadlineField extends C4GBrickField
{
    private $additionalHeaderText = '';
    private $showHeadlineNumber = false;
    private $showFieldCount = false;
    private $accordionOpened = false;

    /**
     * @param $fieldList
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $result = '';

        if ($this->isShowIfEmpty() || !empty($this->getTitle())) {

            $condition = $this->createConditionData($fieldList, $data);

            $accordion = '';
            $tabContainer = '';
            $accordion_content = '';
            $tabContent = '';
            $headline_count = 0;
            $tablist = array();
            foreach($fieldList as $field) {
                if ((get_class($field) == 'con4gis\ProjectsBundle\Classes\Fieldtypes\C4GHeadlineField')
                    && ($field->isFormField())) {
                    $headline_count++;
                    $tablist[] = $field;
                }
            }

            $headlineText = $this->getTitle();
            if ($this->showHeadlineNumber) {
                $headlineText = $headline_count.'. '.$headlineText;
            }

            $headline =
                '<h2 class="c4g_brick_headline" '.$condition['conditionPrepare'].'>' . $headlineText . '</h2>';
            $class = 'formdata ';
            $headline = $this->generateC4GFieldHTML($condition, $headline . $this->additionalHeaderText, $class);
            /*'<div id="c4g_condition" '
            . $class
            . $condition['conditionName']
            . $condition['conditionType']
            . $condition['conditionValue']
            . $condition['conditionFunction']
            . $condition['conditionDisable']
            . '>'
            . $headline
            . $this->additionalHeaderText
            . '</div>';*/
            if ($dialogParams->isAccordion()) {
                if ($dialogParams->getAccordionCounter() > 0) {
                    if ($dialogParams->getAccordionCounter() >= $headline_count) {
                        //last headline
                        $accordion = '</div><br><div class ="c4gGuiAccordion ui-accordion ui-corner-top c4gGuiCollapsible_trigger">';
                    } else {
                        $accordion = '</div><br><div class ="c4gGuiAccordion ui-accordion ui-corner-top c4gGuiCollapsible_trigger">';
                    }
                } else {
                    //first headline
                    $accordion =  '<div class="c4gGuiAccordion ui-accordion ui-corner-top c4gGuiCollapsible_trigger">';
                }

                $accordion_state = 'c4gGuiCollapsible_target c4gGuiCollapsible_hide';
                if ($dialogParams->isAccordionAllOpened()  || ($this->accordionOpened)) {
                    $accordion_state = 'c4gGuiCollapsible_target';
                }
                $accordion_content = '</div><div class="'.$accordion_state.' ui-accordion-content ui-corner-bottom ui-widget-content"><br>';

                $dialogParams->setAccordionCounter($dialogParams->getAccordionCounter()+1);
                $headline = '<h3 class="c4g_brick_headline ui-accordion-header ui-corner-top ui-accordion-icons c4gGuiCollapsible_trigger_target"><a href="#">' . $this->getTitle() . '</a></h3>';
                $class = 'formdata ';
                $headline = $this->generateC4GFieldHTML($condition, $headline . $this->additionalHeaderText, $class);
                    /*'<div id="c4g_condition" '
                    . $class
                    . $condition['conditionName']
                    . $condition['conditionType']
                    . $condition['conditionValue']
                    . $condition['conditionFunction']
                    . $condition['conditionDisable']
                    . '>'
                    . $headline
                    . $this->additionalHeaderText
                    . '</div>';*/
            } else if ($dialogParams->isTabContent()) {
                $tabField = "c4g_tab_".$dialogParams->getTabContentCounter()."_content";
                if ($dialogParams->getTabContentCounter() > 0) {
                    //hotfix for showing tabContent with visibility hidden
                    $margin = 2+($dialogParams->getTabContentCounter() * 0.052);
                    if ($dialogParams->getTabContentCounter() >= $headline_count) {
                        //last headline
                        $tabContent = '<br></div></div><div style="margin-top:-'.$margin.'px" class="'.$tabField.' c4gGuiTabContent ui-corner-bottom ui-widget-content">'.$headline;
                    } else {
                        $tabContent = '<br></div><div style="margin-top:-'.$margin.'px" class="'.$tabField.' c4gGuiTabContent ui-corner-bottom ui-widget-content">'.$headline;
                    }
                    $headline = '';
                } else {
                    $tabContainer = '<div class="c4gGuiTabContainer ui-tabs ui-corner-all ui-widget ui-widget-content"><ul class="c4gGuiTabLinks ui-widget ui-tabs-nav ui-corner-all">';
                    $idx = 0;
                    foreach ($tablist as $tab) {
                        $tabFieldValue = "c4g_tab_".$idx;
                        $tabTitle = $tab->getTitle();
                        if ($idx == 0) {
                            $tabContainer .= '<li class="c4gGuiTabLink ui-tabs-anchor ui-tabs-tab ui-corner-top ui-state-active ui-tab '.$tabFieldValue.'" onclick="clickC4GTab(\''.$tabFieldValue.'\')" data-tab="'.$tabFieldValue.'">'.$tabTitle.'</li>';
                        } else {
                            $tabContainer .= '<li class="c4gGuiTabLink ui-tabs-anchor ui-tabs-tab ui-corner-top ui-state-default ui-tab '.$tabFieldValue.'" onclick="clickC4GTab(\''.$tabFieldValue.'\')" data-tab="'.$tabFieldValue.'">'.$tabTitle.'</li>';
                        }
                        $idx++;
                    }
                    $tabContainer .= '</ul>';
                    //first headline
                    $tabContent =  '<div class="'.$tabField.' c4gGuiTabContent ui-corner-bottom ui-widget-content current">'.$headline;
                    $headline = '';
                }
                $dialogParams->setTabContentCounter($dialogParams->getTabContentCounter()+1);
            }

            $result =
                $accordion
                . $tabContainer
                . $headline
                . $accordion_content
                . $tabContent;
        }
        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {

    }

    /**

     * Public method for creating the field specific tile HTML
     * @param $fieldTitle
     * @param $element
     * @return mixed
     */
    public function getC4GTileField($fieldTitle, $element)
    {
        return $fieldTitle . '<div class="c4g_tile value">' . '<h3>'.$this->getTitle().'</h3>' . '</div>';
    }

    /**
     * @return string
     */
    public function getAdditionalHeaderText()
    {
        return $this->additionalHeaderText;
    }

    /**
     * @param string $additionalHeaderText
     */
    public function setAdditionalHeaderText($additionalHeaderText)
    {
        $this->additionalHeaderText = $additionalHeaderText;
    }

    /**
     * @return boolean
     */
    public function isShowFieldCount()
    {
        return $this->showFieldCount;
    }

    /**
     * @param boolean $showFieldCount
     */
    public function setShowFieldCount($showFieldCount)
    {
        $this->showFieldCount = $showFieldCount;
    }

    /**
     * @return boolean
     */
    public function isShowHeadlineNumber()
    {
        return $this->showHeadlineNumber;
    }

    /**
     * @param boolean $showHeadlineNumber
     */
    public function setShowHeadlineNumber($showHeadlineNumber)
    {
        $this->showHeadlineNumber = $showHeadlineNumber;
    }
    /**
     * @return bool
     */
    public function isAccordionOpened()
    {
        return $this->accordionOpened;
    }

    /**
     * @param bool $accordionOpened
     */
    public function setAccordionOpened($accordionOpened)
    {
        $this->accordionOpened = $accordionOpened;
    }


}