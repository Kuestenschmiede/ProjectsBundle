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

use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;

class C4GBrickButton
{
    private $caption = '';
    private $type    = null;
    private $enabled = true;
    private $visible = true;
    private $action  = '';
    private $accesskey = '';
    private $defaultByEnter = false;
    private $notification = null;
    private $condition = null; //see BrickCondition boolSwitch
    private $additionalCssClass = '';

    /**
     * C4GBrickButton constructor.
     * @param string $caption
     * @param null $type
     * @param bool $visible
     * @param bool $enabled
     */
    public function __construct($type, $caption='', $visible=true, $enabled=true, $action='', $accesskey='', $defaultByEnter=false, $notification=null, $condition=null, $additionalClass = '')
    {
        if ($caption == '') {
            $caption = $this->getTypeCaption($type);
        }

        if ($action == '') {
            $action = $this->getTypeAction($type);
        }

        $this->caption = $caption;
        $this->type    = $type;
        $this->visible = $visible && $caption;
        $this->enabled = $enabled;
        $this->action  = $action;
        $this->accesskey = $accesskey;
        $this->defaultByEnter = $defaultByEnter;
        $this->notification = $notification;
        $this->condition = $condition;
        $this->additionalCssClass = $additionalClass;
    }

    /**
     * @param $type
     * @return string
     */
    public function getTypeCaption($type) {
        $caption = '';


        switch($type) {
            case C4GBrickConst::BUTTON_ADD:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['CREATEELEMENT'];
                break;
            case C4GBrickConst::BUTTON_GROUP:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['SELECTGROUP'];
                break;
            case C4GBrickConst::BUTTON_PROJECT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['SELECTPROJECT'];
                break;
            case C4GBrickConst::BUTTON_PARENT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['SELECTPARENT'];
                break;
            case C4GBrickConst::BUTTON_FILTER:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['SELECTFILTER'];
                break;
            case C4GBrickConst::BUTTON_PRINTLIST:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['PRINTLIST'];
                break;
            case C4GBrickConst::BUTTON_IMPORT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_LIST']['SELECTIMPORT'];
                break;
            case C4GBrickConst::BUTTON_SAVE:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SAVE'];
                break;
            case C4GBrickConst::BUTTON_TICKET:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['TICKET'];
                break;
            case C4GBrickConst::BUTTON_SAVE_AND_NEW:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SAVE_AND_NEW'];
                break;
            case C4GBrickConst::BUTTON_SAVE_AND_REDIRECT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SAVE_AND_REDIRECT'];
                break;
            case C4GBrickConst::BUTTON_BOOKING_SAVE:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BOOKING_SAVE'];
                break;
            case C4GBrickConst::BUTTON_BOOKING_CHANGE:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BOOKING_CHANGE'];
                break;
            case C4GBrickConst::BUTTON_ARCHIVE:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['ARCHIVE'];
                break;
            case C4GBrickConst::BUTTON_ACTIVATION:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['ACTIVATION'];
                break;
            case C4GBrickConst::BUTTON_SEND_EMAIL:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SEND_EMAIL'];
                break;
            case C4GBrickConst::BUTTON_SEND_NOTIFICATION:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SEND_NOTIFICATION'];
                break;
            case C4GBrickConst::BUTTON_FREEZE:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['FREEZE'];
                break;
            case C4GBrickConst::BUTTON_DEFROST:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['DEFROST'];
                break;
            case C4GBrickConst::BUTTON_DELETE:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['DELETE'];
                break;
            case C4GBrickConst::BUTTON_CANCEL:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['CANCEL'];
                break;
            case C4GBrickConst::BUTTON_EXPORT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['EXPORT'];
                break;
            case C4GBrickConst::BUTTON_PRINT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['PRINT'];
                break;
            //not ready
            case C4GBrickConst::BUTTON_POPUP:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['POPUP'];
                break;
            case C4GBrickConst::BUTTON_REDIRECT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['REDIRECT'];
                break;
            case C4GBrickConst::BUTTON_CLICK:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['CLICK'];
                break;
            case C4GBrickConst::BUTTON_NEXT:
                $caption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['NEXT'];
                break;
        }

        return $caption;
    }

    /**
     * @param $type
     * @return string
     */
    public function getTypeAction($type) {
        $action = '';
        switch($type) {
            case C4GBrickConst::BUTTON_ADD:
                $action = C4GBrickActionType::ACTION_SHOWDIALOG;
                break;
            case C4GBrickConst::BUTTON_REDIRECT_BACK:
                $action = C4GBrickActionType::ACTION_REDIRECTBACK;
                break;
            case C4GBrickConst::BUTTON_GROUP:
                $action = C4GBrickActionType::ACTION_SELECTGROUP;
                break;
            case C4GBrickConst::BUTTON_PROJECT:
                $action = C4GBrickActionType::ACTION_SELECTPROJECT;
                break;
            case C4GBrickConst::BUTTON_PARENT:
                $action = C4GBrickActionType::ACTION_SELECTPARENT;
                break;
            case C4GBrickConst::BUTTON_FILTER:
                $action = C4GBrickActionType::ACTION_FILTER;
                break;
            case C4GBrickConst::BUTTON_PRINTLIST:
                $action = C4GBrickActionType::ACTION_PRINTLIST;
                break;
            case C4GBrickConst::BUTTON_IMPORT:
                $action = C4GBrickActionType::ACTION_IMPORT;
                break;
            case C4GBrickConst::BUTTON_SAVE:
                $action = C4GBrickActionType::ACTION_SAVEDIALOG;
                break;
            case C4GBrickConst::BUTTON_TICKET:
                $action = C4GBrickActionType::ACTION_TICKET;
                break;
            case C4GBrickConst::BUTTON_SAVE_AND_NEW:
                $action = C4GBrickActionType::ACTION_SAVEANDNEWDIALOG;
                break;
            case C4GBrickConst::BUTTON_SAVE_AND_REDIRECT:
                $action = C4GBrickActionType::ACTION_SAVEANDREDIRECTDIALOG;
                break;
            case C4GBrickConst::BUTTON_BOOKING_SAVE:
                $action = C4GBrickActionType::ACTION_SAVEDIALOG;
                break;
            case C4GBrickConst::BUTTON_BOOKING_CHANGE:
                $action = C4GBrickActionType::ACTION_SAVEDIALOG;
                break;
            case C4GBrickConst::BUTTON_ARCHIVE:
                $action = C4GBrickActionType::ACTION_ARCHIVEDIALOG;
                break;
            case C4GBrickConst::BUTTON_ACTIVATION:
                $action = C4GBrickActionType::ACTION_ACTIVATIONDIALOG;
                break;
            case C4GBrickConst::BUTTON_SEND_EMAIL:
                $action = C4GBrickActionType::ACTION_EMAILNOTIFICATIONDIALOG;
                break;
            case C4GBrickConst::BUTTON_SEND_NOTIFICATION:
                $action = C4GBrickActionType::ACTION_SEND_NOTIFICATION;
                break;
            case C4GBrickConst::BUTTON_FREEZE:
                $action = C4GBrickActionType::ACTION_FREEZEDIALOG;
                break;
            case C4GBrickConst::BUTTON_DEFROST:
                $action = C4GBrickActionType::ACTION_DEFROSTDIALOG;
                break;
            case C4GBrickConst::BUTTON_DELETE:
                $action = C4GBrickActionType::ACTION_DELETEDIALOG;
                break;
            case C4GBrickConst::BUTTON_CANCEL:
                $action = C4GBrickActionType::ACTION_CLOSEDIALOG;
                break;
            case C4GBrickConst::BUTTON_EXPORT:
                $action = C4GBrickActionType::ACTION_EXPORT;
                break;
            case C4GBrickConst::BUTTON_PRINT:
                $action = C4GBrickActionType::ACTION_PRINT;
                break;
            //not ready
            case C4GBrickConst::BUTTON_POPUP:
                $action = C4GBrickActionType::ACTION_POPUP;
                break;
            case C4GBrickConst::BUTTON_REDIRECT:
                $action = C4GBrickActionType::ACTION_REDIRECT;
                break;
            case C4GBrickConst::BUTTON_CLICK:
                $action = C4GBrickActionType::ACTION_BUTTONCLICK;
                break;
        }

        return $action;
    }

    /**
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param string $caption
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return boolean
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAccesskey()
    {
        return $this->accesskey;
    }

    /**
     * @param string $accesskey
     */
    public function setAccesskey($accesskey)
    {
        $this->accesskey = $accesskey;
    }

    /**
     * @return boolean
     */
    public function isDefaultByEnter()
    {
        return $this->defaultByEnter;
    }

    /**
     * @param boolean $defaultByEnter
     */
    public function setDefaultByEnter($defaultByEnter)
    {
        $this->defaultByEnter = $defaultByEnter;
    }

    /**
     * @return null
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * @param null $notification
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return null
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param null $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getAdditionalCssClass(): string
    {
        return $this->additionalCssClass;
    }

    /**
     * @param string $additionalCssClass
     */
    public function setAdditionalCssClass(string $additionalCssClass)
    {
        $this->additionalCssClass = $additionalCssClass;
    }
}