<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GButtonField extends C4GBrickField
{
    private $button = null; //siehe C4GBrickButton
    private $circleButton = false;
    private $onClick = '';
    private $onClickType = C4GBrickConst::ONCLICK_TYPE_SERVER;
    private $overlay = null;
    private $color = ''; //standardmäßig greift das CSS

    /**
     * C4GButtonField constructor.
     */
    public function __construct(C4GBrickButton $button, string $type = C4GBrickFieldType::BUTTON)
    {
        $this->setDatabaseField(false);
        $this->setComparable(false);
        $this->setTitle($button->getCaption());

        $this->button = $button;

        parent::__construct($type);
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);

        $button = $this->button;

        $divBefore = '';
        $divAfter = '';

        if ($button->getAction() && ($button->getType() == C4GBrickConst::BUTTON_CLICK) && $this->getOnClick()) {
            $function = $this->getOnClick();
            $divBefore = '<div class="c4gGuiDialogButtonsJqui ">';
            $divAfter = '</div>';
            $class = 'c4gGuiAction c4gGuiButton c4g__btn c4g__btn-primary c4gGuiSend';

            if ($this->getOnClickType() == C4GBrickConst::ONCLICK_TYPE_SERVER) {
                $dataAction = 'href="#" data-action="' . $button->getAction() . ':' . $function . ':' . $dialogParams->getId() . '" role="button"';
            } else {
                $dataAction = 'href="#" onClick="this.submit;' . $function . '"';
            }
        } elseif ($button->getAction()) {
            $divBefore = '<div class="c4gGuiDialogButtonsJqui ">';
            $divAfter = '</div>';
            $class = 'c4gGuiAction c4gGuiButton c4g__btn c4g__btn-primary c4gGuiSend';

            $dataAction = 'href="#" data-action="' . $button->getAction() . ':' . $dialogParams->getId() . '" role="button"';
        } else {
            $value = $this->generateInitialValue($data);
            $class = 'formdata c4g_brick_button';
            $dataAction = 'href="' . $value . '"';
        }

        if ($this->isCircleButton()) {
            $class = $class . ' c4gCircleButton';
        }

        $accesskey = '';
        if ($button->getAccesskey()) {
            $accesskey = ' accesskey="' . $button->getAccesskey() . '"';
        }
        $div = '';
        $enddiv = '';
        $condition = $this->createConditionData($fieldList, $data);

        $style = '';
        if ($this->color) {
            $style = ' style="background-color: ' . $this->color . '"';
        }

        $result = $div .
            $this->addC4GField($condition,$dialogParams,$fieldList,$data,
            $divBefore . '<a ' . $dataAction . ' ' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" ' . $accesskey . ' class="' . $class . '"' . $style . ' >' . $this->getTitle() . '</a>' . $divAfter . $enddiv);

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
    }

    /**
     * @return null
     */
    public function getButton()
    {
        return $this->button;
    }

    /**
     * @param $button
     * @return $this
     */
    public function setButton($button)
    {
        $this->button = $button;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isCircleButton()
    {
        return $this->circleButton;
    }

    /**
     * @param $circleButton
     * @return $this
     */
    public function setCircleButton($circleButton)
    {
        $this->circleButton = $circleButton;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        return $this->onClick;
    }

    /**
     * @param $onClick
     * @return $this
     */
    public function setOnClick($onClick)
    {
        $this->onClick = $onClick;

        return $this;
    }

    /**
     * @return null
     */
    public function getOverlay()
    {
        return $this->overlay;
    }

    /**
     * @param $overlay
     * @return $this
     */
    public function setOverlay($overlay)
    {
        $this->overlay = $overlay;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param $color
     * @return $this
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnClickType()
    {
        return $this->onClickType;
    }

    /**
     * @param $onClickType
     * @return $this
     */
    public function setOnClickType($onClickType)
    {
        $this->onClickType = $onClickType;

        return $this;
    }
}
