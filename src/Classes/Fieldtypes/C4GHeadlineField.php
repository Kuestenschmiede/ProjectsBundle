<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

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
    protected $associatedFields = [];
    protected $number = 0;

    const ACCORDION_HEADLINE_CLASS = 'c4g__accordion-headline';
    const ACCORDION_HEADLINE_ICON_CLASS = 'c4g__accordion-icon';
    const ACCORDION_HEADLINE_TEXT_CLASS = 'c4g__accordion-text';
    const ACCORDION_TARGET_CLASS = 'c4g__accordion-target';
    const ACCORDION_HEADLINE_STATE_ACTIVE = 'c4g__accordion-headline-active';
    const ACCORDION_TARGET_STATE_ACTIVE = 'c4g__accordion-target-active';

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::HEADLINE)
    {
        parent::__construct($type);
    }

    /**
     * @param $fieldList
     * @param $data
     * @param $dialogParams
     * @param $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $result = '';
        if ($this->associatedFields !== []) {
            $hasValues = false;

            foreach ($this->associatedFields as $associatedField) {
                $fieldName = $associatedField->getFieldName();
                if (!empty($data->$fieldName) && trim($data->$fieldName) !== '') {
                    if ($associatedField instanceof C4GMultiLinkField || $associatedField instanceof C4GMultiCheckboxField) {
                        $arrayData = \StringUtil::deserialize($data->$fieldName);
                        foreach ($arrayData as $row) {
                            foreach ($row as $key => $item) {
                                if (trim($item) !== '') {
                                    $hasValues = true;
                                }
                            }
                        }
                    } else {
                        $hasValues = true;
                    }
                }
            }

            if ($hasValues === false) {
                return '';
            }
        }

        if ($this->isShowIfEmpty() || !empty($this->getTitle())) {
            $condition = $this->createConditionData($fieldList, $data);

            $accordion = '';
            $tabContainer = '';
            $accordion_content = '';
            $tabContent = '';
            $headline_count = 0;
            $tablist = [];
            foreach ($fieldList as $field) {
                if (($field instanceof C4GHeadlineField) && ($field->isFormField())) {
                    $headline_count++;
                    $tablist[] = $field;
                }
            }

            $headlineText = $this->getTitle();
            if ($this->showHeadlineNumber) {
                $headlineText = $headline_count . '. ' . $headlineText;
            }

            $styleClass = 'c4g__form-'.$this->getType().' '.'c4g__form-'.$this->getType().'--'.$this->getFieldName();
            $headline =
                '<div class="c4g__form-headline" ' . $condition['conditionPrepare'] . '>' . $headlineText . '</div>';
            $class = 'class="c4g__form-group formdata '.$styleClass.'"';
            $headline = '<div '
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
                $accordionClass = 'c4gGuiAccordion c4g__accordion c4gGuiCollapsible_trigger';

                if ($dialogParams->getAccordionCounter() > 0) {
                    if ($dialogParams->getAccordionCounter() >= $headline_count) {
                        //last headline
                        $accordion = '</div><br><div class ="' . $accordionClass . '">';
                    } else {
                        $accordion = '</div><br><div class ="' . $accordionClass . '">';
                    }
                } else {
                    //first headline
                    $accordion = '<div class="' . $accordionClass . '">';
                }

                $accordion_content = '</div><div class="' . $accordion_state . ' c4g__accordion-content c4g__btn c4g__btn-accordion c4g__content"><br>';
                $icon = $this->getAccordionIcon();

                $dialogParams->setAccordionCounter($dialogParams->getAccordionCounter() + 1);
                $headline = '<h3 class="c4g__form-headline c4g__accordion-header c4g__accordion-icons c4gGuiCollapsible_trigger_target"><a href="#">' . $icon . $this->getTitle() . '</a></h3>';
                $styleClass = 'c4g__form-'.$this->getType().' '.'c4g__form-'.$this->getType().'--'.$this->getFieldName();
                $class = 'class="c4g__form-group formdata '.$styleClass.'"';
                $headline = '<div '
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
            } elseif ($dialogParams->isTabContent()) {
                $tabField = 'c4g_tab_' . $dialogParams->getTabContentCounter() . '_content';
                if ($dialogParams->getTabContentCounter() > 0) {
                    //hotfix for showing tabContent with visibility hidden
                    $margin = 2 + ($dialogParams->getTabContentCounter() * 0.052);
                    if ($dialogParams->getTabContentCounter() >= $headline_count) {
                        //last headline
                        $tabContent = '<br></div></div><div style="margin-top:-' . $margin . 'px" class="' . $tabField . ' c4gGuiTabContent c4g__content">' . $headline;
                    } else {
                        $tabContent = '<br></div><div style="margin-top:-' . $margin . 'px" class="' . $tabField . ' c4gGuiTabContent c4g__content">' . $headline;
                    }
                    $headline = '';
                } else {
                    $tabContainer = '<div class="c4gGuiTabContainer c4g__tabs c4g__content"><ul class="c4gGuiTabLinks c4g__tabs-nav">';
                    $idx = 0;
                    foreach ($tablist as $tab) {
                        $tabFieldValue = 'c4g_tab_' . $idx;
                        $tabTitle = $tab->getTitle();
                        if ($idx == 0) {
                            $tabContainer .= '<li class="c4gGuiTabLink c4g__tabs-anchor c4g__tabs-tab c4g__state-active ' . $tabFieldValue . '" onclick="clickC4GTab(\'' . $tabFieldValue . '\')" data-tab="' . $tabFieldValue . '">' . $tabTitle . '</li>';
                        } else {
                            $tabContainer .= '<li class="c4gGuiTabLink c4g__tabs-anchor c4g__tabs-tab c4g__state-default ' . $tabFieldValue . '" onclick="clickC4GTab(\'' . $tabFieldValue . '\')" data-tab="' . $tabFieldValue . '">' . $tabTitle . '</li>';
                        }
                        $idx++;
                    }
                    $tabContainer .= '</ul>';
                    //first headline
                    $tabContent = '<div class="' . $tabField . ' c4gGuiTabContent ui-corner-bottom ui-widget-content current">' . $headline;
                    $headline = '';
                }
                $dialogParams->setTabContentCounter($dialogParams->getTabContentCounter() + 1);
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

    public function getC4GListField($rowData, $content)
    {
        if ($this->associatedFields !== []) {
            $hasValues = false;

            foreach ($this->associatedFields as $associatedField) {
                $fieldName = $associatedField->getFieldName();
                if (!empty($rowData->$fieldName) && trim($rowData->$fieldName) !== '') {
                    if ($associatedField instanceof C4GMultiLinkField || $associatedField instanceof C4GMultiCheckboxField) {
                        $arrayData = \StringUtil::deserialize($rowData->$fieldName);
                        foreach ($arrayData as $row) {
                            foreach ($row as $key => $item) {
                                if (trim($item) !== '') {
                                    $hasValues = true;
                                }
                            }
                        }
                    } else {
                        $hasValues = true;
                    }
                }
            }

            if ($hasValues === false) {
                return '';
            }
        }

        if ($this->number > 0) {
            $class = 'c4g_list_headline c4g_list_headline_' . $this->number;
        } else {
            $class = 'c4g_list_headline';
        }

        return '<span class="' . $class . '">' . $this->getTitle() . '</span>';
    }

    /**
     * @param $data
     * @param $groupId
     * @return string
     */
    public function getC4GPopupField($data, $groupId)
    {
        if ($this->getTitle()) {
            return '<div class="c4g_popup_headline">' . $this->getTitle() . '</div>';
        }

        return '';
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
        return $fieldTitle . '<div class="c4g_tile_value">' . '<h3>' . $this->getTitle() . '</h3>' . '</div>';
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

    public function addAssociatedField(C4GBrickField $brickField)
    {
        $this->associatedFields[] = $brickField;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     * @return C4GHeadlineField
     */
    public function setNumber(int $number): C4GHeadlineField
    {
        $this->number = $number;

        return $this;
    }
}
