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

use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainer;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GStopwatchField extends C4GBrickField
{
    private $seconds = 60;
    private $runningOutAction = null;
    private $runningOutId = '';

    private $overlay = null; //C4GBrickOverlay

    /**
     * C4GStopwatchField constructor.
     */
    public function __construct($seconds = 60)
    {
        $this->seconds = $seconds;
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, C4GContainer $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $result = '';

        $seconds = $this->seconds;
        if  ($seconds) {

            $condition = $this->createConditionData($fieldList, $data);

            $overlay_html = '';
            $overlay_id = '';
            if ($this->overlay) {
                $overlay_id = $this->overlay->getId();
                $overlay_html = $this->overlay->getHtml();
                $overlay_link = $this->overlay->getLink();
            }

            $action = '';
            if ($this->runningOutAction == C4GBrickActionType::ACTION_RESTART) {
                $action = '<a href="#" id="'.$id.'_action" class="c4gGuiAction c4g_brick_timer_action c4gGuiAction" style="display:none" onClick="stopwatch(\'' . $id . '\',\''.$seconds.'\',\''.$overlay_id.'\',\''.$overlay_link.'\')" role="button" ></a>';
            } else if ($this->runningOutAction) {
                if ($this->runningOutId == '') {
                    $this->runningOutId = $dialogParams->getId();
                }
                $action = '<a href="#" id="'.$id.'_action" class="c4gGuiAction c4g_brick_timer_action c4gGuiAction c4gGuiButton c4gGuiSend c4g_brick_button" style="display:none" data-action="' . $this->runningOutAction. ':'.$this->runningOutId.'" role="button" ></a>';
            }

            $html =
                '<div id="'.$id.'" class="c4g_brick_stopwatch c4gGuiDialogButtonsJqui" onClick="stopwatch(\'' . $id . '\',\''.$seconds.'\',\''.$overlay_id.'\',\''.$overlay_link.'\')">'.
                '</div><script>jQuery("#'.$id.'").click();</script>'.$action.$overlay_html;

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $html);
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
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param $seconds
     * @return $this
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;
        return $this;
    }

    /**
     * @return null
     */
    public function getRunningOutAction()
    {
        return $this->runningOutAction;
    }

    /**
     * @param $runningOutAction
     * @return $this
     */
    public function setRunningOutAction($runningOutAction)
    {
        $this->runningOutAction = $runningOutAction;
        return $this;
    }

    /**
     * @return int
     */
    public function getRunningOutId()
    {
        return $this->runningOutId;
    }

    /**
     * @param $runningOutId
     * @return $this
     */
    public function setRunningOutId($runningOutId)
    {
        $this->runningOutId = $runningOutId;
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
}