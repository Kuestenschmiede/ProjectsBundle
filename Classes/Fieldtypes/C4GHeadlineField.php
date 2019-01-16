<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
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
    protected $additionalHeaderText = '';
    protected $showHeadlineNumber = false;
    protected $showFieldCount = false;
    protected $accordionIcon = ''; //HTML of the icon to be displayed before the title.

    const ACCORDION_HEADLINE_CLASS = 'c4g_accordion_headline';
    const ACCORDION_HEADLINE_ICON_CLASS = 'c4g_accordion_icon';
    const ACCORDION_HEADLINE_TEXT_CLASS = 'c4g_accordion_text';
    const ACCORDION_TARGET_CLASS = 'c4g_accordion_target';
    const ACCORDION_HEADLINE_STATE_ACTIVE = 'c4g_accordion_headline_active';
    const ACCORDION_TARGET_STATE_ACTIVE = 'c4g_accordion_target_active';

    /**
     * @param $fieldList
     * @param $data
     * @param $dialogParams
     * @param $additionalParams
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
                if (($field instanceof C4GHeadlineField) && ($field->isFormField())) {
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
//            $headline = $this->generateC4GFieldHTML($condition, $headline . $this->additionalHeaderText, $class);
            $headline = '<div id="c4g_condition" '
                . $class
                . $condition['conditionName']
                . $condition['conditionType']
                . $condition['conditionValue']
                . $condition['conditionFunction']
                . $condition['conditionDisable']
                . '>'
                . $headline
                . $this->additionalHeaderText
                . '</div>';
            if ($dialogParams->isAccordion()) {
                $accordion_state = 'c4gGuiCollapsible_target c4gGuiCollapsible_hide';
                $accordionClass = 'c4gGuiAccordion ui-accordion ui-corner-top c4gGuiCollapsible_trigger';

                if ($dialogParams->getAccordionCounter() > 0) {
                    if ($dialogParams->getAccordionCounter() >= $headline_count) {
                        //last headline
                        $accordion = '</div><br><div class ="' .  $accordionClass . '">';
                    } else {
                        $accordion = '</div><br><div class ="' .  $accordionClass . '">';
                    }
                } else {
                    //first headline
                    $accordion =  '<div class="' .  $accordionClass . '">';
                }

                $accordion_content = '</div><div class="'.$accordion_state.' ui-accordion-content ui-corner-bottom ui-widget-content"><br>';
                $icon = $this->getAccordionIcon();
                if ($icon == '') {
                    $icon = $dialogParams->getAccordionIcon();
                }

                $dialogParams->setAccordionCounter($dialogParams->getAccordionCounter()+1);
                $headline = '<h3 class="c4g_brick_headline ui-accordion-header ui-corner-top ui-accordion-icons c4gGuiCollapsible_trigger_target"><a href="#">' . $icon . $this->getTitle() . '</a></h3>';
                $class = 'formdata ';
                $headline = '<div id="c4g_condition" '
                    . $class
                    . $condition['conditionName']
                    . $condition['conditionType']
                    . $condition['conditionValue']
                    . $condition['conditionFunction']
                    . $condition['conditionDisable']
                    . '>'
                    . $headline
                    . $this->additionalHeaderText
                    . '</div>';
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
     * @param $data
     * @param $groupId
     * @return string
     */
    public function getC4GPopupField($data, $groupId)
    {
       if ($this->getTitle()) {
           return '<div class="c4g_popup_headline"><h2>'. $this->getTitle() .'</h2></div>';
       } else {
           return '';
       }
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
     * @param $additionalHeaderText
     * @return $this
     */
    public function setAdditionalHeaderText($additionalHeaderText)
    {
        $this->additionalHeaderText = $additionalHeaderText;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowFieldCount()
    {
        return $this->showFieldCount;
    }

    /**
     * @param $showFieldCount
     * @return $this
     */
    public function setShowFieldCount($showFieldCount)
    {
        $this->showFieldCount = $showFieldCount;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowHeadlineNumber()
    {
        return $this->showHeadlineNumber;
    }

    /**
     * @param $showHeadlineNumber
     * @return $this
     */
    public function setShowHeadlineNumber($showHeadlineNumber)
    {
        $this->showHeadlineNumber = $showHeadlineNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccordionIcon()
    {
        return $this->accordionIcon;
    }

    /**
     * @param $accordionIcon
     * @return $this
     */
    public function setAccordionIcon($accordionIcon)
    {
        $this->accordionIcon = $accordionIcon;
        return $this;
    }


}