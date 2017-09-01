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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

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
    public function __construct(C4GBrickButton $button)
    {
        $this->setDatabaseField(false);
        $this->setComparable(false);
        $this->setTitle($button->getCaption());

        $this->button = $button;
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);

        $button = $this->button;

        $divBefore = '';
        $divAfter  = '';

        if ($button->getAction() && ($button->getType() == C4GBrickConst::BUTTON_CLICK) && $this->getOnClick()) {
            $function = $this->getOnClick();
            $divBefore = '<div class="c4gGuiDialogButtonsJqui ">';
            $divAfter  = '</div>';
            $class = 'c4gGuiAction c4gGuiButton c4g_brick_button c4gGuiSend';

            if ($this->getOnClickType() == C4GBrickConst::ONCLICK_TYPE_SERVER) {
                $dataAction = 'href="#" data-action="' . $button->getAction(). ':' . $function . '" role="button"';
            } else {
                $dataAction = 'href="#" onClick="this.submit;'.$function.'"';
            }
        } else if ($button->getAction()) {
          $divBefore = '<div class="c4gGuiDialogButtonsJqui ">';
          $divAfter  = '</div>';
          $class = 'c4gGuiAction c4gGuiButton c4g_brick_button c4gGuiSend';

          $dataAction = 'href="#" data-action="' . $button->getAction(). ':' . $dialogParams->getId() . '" role="button"';
        } else {
          $value = $this->generateInitialValue($data);
          $class = 'formdata c4g_brick_button';
          $dataAction = 'href="' . $value . '"';
        }

        if($this->isCircleButton()){
            $class = $class.' c4gCircleButton';
        }

        $accesskey = '';
        if ($button->getAccesskey()) {
            $accesskey = ' accesskey="'.$button->getAccesskey().'"';
        }
        $div = '';
        $enddiv = '';
        $condition = $this->createConditionData($fieldList, $data);

        $style = '';
        if ($this->color) {
            $style = ' style="background-color: '.$this->color.'"';
        }

        $result = $div.
            $this->addC4GField($condition,$dialogParams,$fieldList,$data,
            $divBefore .'<a ' .$dataAction .' '. $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" '.$accesskey.' class="'.$class.'"'.$style.' >' .$this->getTitle() . '</a>' . $divAfter . $enddiv);

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
     * @param null $button
     */
    public function setButton($button)
    {
        $this->button = $button;
    }

    /**
     * @return boolean
     */
    public function isCircleButton()
    {
        return $this->circleButton;
    }

    /**
     * @param boolean $circleButton
     */
    public function setCircleButton($circleButton)
    {
        $this->circleButton = $circleButton;
    }

    /**
     * @return string
     */
    public function getOnClick()
    {
        return $this->onClick;
    }

    /**
     * @param string $onClick
     */
    public function setOnClick($onClick)
    {
        $this->onClick = $onClick;
    }

    /**
     * @return null
     */
    public function getOverlay()
    {
        return $this->overlay;
    }

    /**
     * @param null $overlay
     */
    public function setOverlay($overlay)
    {
        $this->overlay = $overlay;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getOnClickType()
    {
        return $this->onClickType;
    }

    /**
     * @param string $onClickType
     */
    public function setOnClickType($onClickType)
    {
        $this->onClickType = $onClickType;
    }
}