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

namespace con4gis\ProjectsBundle\Classes\Dialogs;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;

/**
 * Class C4GBrickDialogParams
 * @package c4g\projects
 */
class C4GBrickDialogParams
{
    private $id = -1; //dialogId
    private $brickKey = ''; //brickKey (module key)
    private $projectKey = '';//project key (parent brickKey)
    private $captionField       = 'caption'; //Feldname für die Benennung eines einzelnen Datensatzes. Kann überschrieben werden.
    private $brickCaption       = ''; //Standard Singular Datensatzbeschriftung. Kann überschrieben werden.
    private $brickCaptionPlural = ''; //Standard Plural Datensatzbeschriftung. Kann überschrieben werden.
    private $memberId = -1; //memberId (user)
    private $groupId = -1; //groupId (membergroup)
    private $projectId = -1; //projectId (if project based or project parent)
    private $projectUuid = ''; //uuid (if project based or project parent)
    private $parentId = -1; //parentId (parent dataset id)
    private $additionalId = -1; //special additional id
    private $additionalIdField = '';//field for special additional id
    private $parentIdField = ''; //id field for parent id
    private $parentModel = ''; //In Verbindung mit ViewType PROJECTPARENTBASED und GROUPPARENTVIEW
    private $parentCaption = ''; //Hinweistexte können sich so im Kontext auf den Parent beziehen.
    private $parentCaptionPlural = ''; //Hinweistexte können sich so im Kontext auf den Parent beziehen.
    private $parentCaptionFields = array(); // Für die Selectbox in der Parentauswahl, sodass die Bezeichnung auch aus einem anderen Feld als caption/name kommen kann
    private $parentCaptionCallback = array(); // Funktion aus dem aktuellen Modul, sodass die Bezeichnungen über einen Callback gesetzt werden
    private $homeDir = ''; //homeDir for saving project data
    private $viewType = ''; //viewType -> see C4GBrickView
    private $viewParams = null; //viewParams for BrickView (params for List and Dialog)
    private $frozen = false; //special locks the dialog
    private $buttons = array(); //dialog buttons
    private $accordion = false; //activates accordion (every headline is an accordeon button)
    private $accordion_counter = 0; //counts the accordions button to calc end of div
    private $accordion_all_opened = false; //opens all accordeon buttons by default
    private $tabContent = false; //activates tabContent (every headline is a tab button)
    private $tabContent_counter = 0; //counts the tab button to calc end of div
    private $withTabContentCheck = true; //tabContentCheck deactivates clear tabs
    private $saveOnMandatory = false; //save dialogdata without mandatory break
    private $mandatoryCheckOnActivate = false; //mandatory check by pushing activation button
    private $redirectWithSaving = true; //saving on redirect
    private $redirectWithActivation = false; //redirect after activation
    private $saveWithoutMessages = false; //no messaging on saving
    private $saveWithoutSavingMessage = false;//save without saving confirmation
    private $withoutGuiHeader = false; // do not show the dialog gui header
    private $headline = ''; //headline of the dialog
    private $withInitialSaving = false; //initial saving by dialog create
    private $redirectSite = null; //site to redirect (redirect button)
    private $redirectBackSite = null; //site to redirect by pushing back button
    private $c4gMap = false; //map content element
    private $contentId = ''; //special content element id
    private $sendEMails = null; //siehe C4GBrickSendEMail
    private $notificationType = null; //notification type
    private $notificationTypeContactRequest = null;
    private $withNotification     = false; //activate notifications
    private $withBackup           = false; //backup see con4gis-Streamer
    private $popup                = false; //shows dialog as magnific popup
    private $withActivationInfo   = false; //activation info
    private $modelListFunction    = null; //Lädt die Datensätze der Tabelle über eine spezielle Modelfunktion.
    private $withLabels           = true; //deactivates all labels
    private $withDescriptions     = true; //deactivates all descriptions
    private $tableRows            = false; //shows label and input in one row
    private $uniqueTitle = ''; //unique message title
    private $uniqueMessage = ''; //unique message
    private $withPrintButton      = false; //activates print button
    private $filterParams = null; //siehe C4GBrickFilterParams
    private $confirmActivation = false;
    private $notifyOnChanges = false;
    private $saveWithoutClose = false;
    private $withNextPrevButtons = false; // zeigt Weiter/Zurück-Buttons im Dialog an, um zwischen Tabs zu wechseln
    private $groupKeyField = '';
    private $onloadScript = ''; // javascript code that should be executed when the dialog is loaded
    private $beforeSaveAction = null; // see C4GBeforeDialogSave
    private $additionalHeadText = '';
    private $isWithEmptyParentOption = false;
    private $isWithCommonParentOption = false;
    private $redirects = array();//C4GBrickRedirect
    private $saveWithUuid = false;
    private $saveTimestamp = true;
    private $Uuid = '';


    /**
     * C4GBrickDialogParams constructor.
     */
    public function __construct($brickKey, $viewType)
    {
        $this->brickKey = $brickKey;
        $this->viewType = $viewType;

        $this->brickCaption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BRICK_CAPTION'];
        $this->brickCaptionPlural = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BRICK_CAPTION_PLURAL'];

        $this->setButtons($this->getDefaultDialogButtons($this->getViewType()));

    }

    /**
     * @return boolean
     */
    public function getDefaultDialogButtons($viewType)
    {
        $buttons = array();

        if ($viewType) {
            if ($viewType != C4GBrickViewType::MEMBERBOOKING) {
                if (!C4GBrickView::isWithoutEditing($viewType)) {
                    $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_SAVE);
                    $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_SAVE_AND_REDIRECT);
//                    $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_TICKET);
                }

                if (!C4GBrickView::isWithoutEditing($viewType) && !C4GBrickView::isWithoutList($viewType)) {
                    //$buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_SAVE_AND_NEW);
                    $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_DELETE);
                }

                if ($viewType == C4GBrickViewType::GROUPPROJECT) {
                    $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_FREEZE);
                    $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_DEFROST);
                }
            } else {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_BOOKING_SAVE);
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_BOOKING_CHANGE);
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_ARCHIVE);
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_ACTIVATION);
            }

            if ( ($viewType != C4GBrickViewType::GROUPFORM) &&
                ($viewType != C4GBrickViewType::GROUPFORMCOPY) &&
                ($viewType != C4GBrickViewType::PROJECTPARENTFORMCOPY) &&
                ($viewType != C4GBrickViewType::PROJECTFORM) &&
                ($viewType != C4GBrickViewType::PROJECTFORMCOPY) &&
                ($viewType != C4GBrickViewType::MEMBERFORM) &&
                ($viewType != C4GBrickViewType::PUBLICFORM)) {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_CANCEL);
            }

            if ($this->withPrintButton) {
                $buttons[] = new C4GBrickButton(C4GBrickConst::BUTTON_PRINT);
            }

        }

        return $buttons;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param int $group_id
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param int $member_id
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param int $project_id
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getProjectUuid()
    {
        return $this->projectUuid;
    }

    /**
     * @param string $project_uuid
     */
    public function setProjectUuid($projectUuid)
    {
        $this->projectUuid = $projectUuid;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parent_id
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return string
     */
    public function getParentIdField()
    {
        return $this->parentIdField;
    }

    /**
     * @param string $parentIdField
     */
    public function setParentIdField($parentIdField)
    {
        $this->parentIdField = $parentIdField;
    }

    /**
     * @return string
     */
    public function getHomeDir()
    {
        return $this->homeDir;
    }

    /**
     * @param string $homeDir
     */
    public function setHomeDir($homeDir)
    {
        $this->homeDir = $homeDir;
    }

    /**
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * @param string $viewType
     */
    public function setViewType($viewType)
    {
        $this->viewType = $viewType;
    }

    /**
     * @return null
     */
    public function getViewParams()
    {
        return $this->viewParams;
    }

    /**
     * @param null $viewParams
     */
    public function setViewParams($viewParams)
    {
        $this->viewParams = $viewParams;
    }

    /**
     * @return boolean
     */
    public function isFrozen()
    {
        return $this->frozen;
    }

    /**
     * @param boolean $frozen
     */
    public function setFrozen($frozen)
    {
        $this->frozen = $frozen;
    }

    public function addButton($type, $caption='', $visible=true, $enabled=true, $action = '', $accesskey = '', $defaultByEnter = false, $notification = null, $condition = null, $additionalClass = '') {

        $exists = false;
        if ($caption == '') {
            $caption = C4GBrickButton::getTypeCaption($type);
        }

        if ($action == '') {
            $action =  C4GBrickButton::getTypeAction($type);
        }

        $button = null;
        if ($type && ($type != C4GBrickConst::BUTTON_CLICK)) {
            foreach($this->buttons as $btn) {
                if ($btn->getType() == $type) {
                    $btn->setCaption($caption);
                    $btn->setVisible($visible);
                    $btn->setEnabled($enabled);
                    $btn->setAction($action);
                    $btn->setAccesskey($accesskey);
                    $btn->setDefaultByEnter($defaultByEnter);
                    $btn->setNotification($notification);
                    $btn->setCondition($condition);
                    $btn->setAdditionalCssClass($additionalClass);
                    $button = $btn;
                    $exists = true;
                    break;
                }
            }
        }

        if (!$exists) {
            $button = new C4GBrickButton(
                $type, $caption, $visible, $enabled, $action, $accesskey, $defaultByEnter, $notification, $condition, $additionalClass);
            $this->buttons[] = $button;
        }

        return $button;
    }

    public function deleteButton($type) {

        $exists  = false;
        foreach($this->buttons as $btn) {
            if ($btn->getType() == $type) {
                $btn->setCaption('');
                $btn->setVisible(false);
                $btn->setEnabled(false);
                $btn->setAction('');
                $btn->setAccesskey('');
                $btn->setDefaultByEnter(false);

                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $this->buttons[] = new C4GBrickButton($type, '', false, false, false, '', '', false);
        }
    }

    public function changeButtonText($type, $caption) {
        foreach($this->buttons as $btn) {
            if ($btn->getType() == $type) {
                $btn->setCaption($caption);
                return true;
            }
        }

        return false;
    }

    public function getButton($type) {
        foreach($this->buttons as $button) {
            if ($button->getType() == $type) {
                return $button;
            }
        }

        return null;
    }

    public function getButtonsArray($type) {
        $result = Array();
        foreach($this->buttons as $button) {
            if ($button->getType() == $type) {
                $result[] = $button;
            }
        }

        return $result;
    }

    public function checkButtonVisibility($type, $dbValues = null) {
        if ($type) {
            foreach ($this->buttons as $button) {
                if ($button->getType() == $type) {
                    $condition = $button->getCondition();
                    if ($condition) {
                        $fieldName = $condition->getFieldName();
                        $value     = $condition->getValue();

                        if ($dbValues && $dbValues->$fieldName) {
                            $button->setVisible($dbValues->$fieldName == $value);
                        } else {
                            $button->setVisible(false);
                        }
                    }

                    if ($button->isVisible()) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param array $buttons
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;
    }

    /**
     * @return boolean
     */
    public function isTableRows()
    {
        return $this->tableRows;
    }

    /**
     * @param boolean $tableRows
     */
    public function setTableRows($tableRows)
    {
        $this->tableRows = $tableRows;
    }

    /**
     * @return boolean
     */
    public function isAccordion()
    {
        return $this->accordion;
    }

    /**
     * @param boolean $accordeon
     */
    public function setAccordion($accordion)
    {
        $this->accordion = $accordion;
    }

    /**
     * @return int
     */
    public function getAccordionCounter()
    {
        return $this->accordion_counter;
    }

    /**
     * @param int $accordion_counter
     */
    public function setAccordionCounter($accordion_counter)
    {
        $this->accordion_counter = $accordion_counter;
    }

    /**
     * @return boolean
     */
    public function isAccordionAllOpened()
    {
        return $this->accordion_all_opened;
    }

    /**
     * @param boolean $accordion_all_opened
     */
    public function setAccordionAllOpened($accordion_all_opened)
    {
        $this->accordion_all_opened = $accordion_all_opened;
    }

    /**
     * @return boolean
     */
    public function isSaveOnMandatory()
    {
        return $this->saveOnMandatory;
    }

    /**
     * @param boolean $saveOnMandatory
     */
    public function setSaveOnMandatory($saveOnMandatory)
    {
        $this->saveOnMandatory = $saveOnMandatory;
    }

    /**
     * @return boolean
     */
    public function isMandatoryCheckOnActivate()
    {
        return $this->mandatoryCheckOnActivate;
    }

    /**
     * @param boolean $mandatoryCheckOnActivate
     */
    public function setMandatoryCheckOnActivate($mandatoryCheckOnActivate)
    {
        $this->mandatoryCheckOnActivate = $mandatoryCheckOnActivate;
    }

    /**
     * @return boolean
     */
    public function isRedirectWithSaving()
    {
        return $this->redirectWithSaving;
    }

    /**
     * @param boolean $redirectWithSaving
     */
    public function setRedirectWithSaving($redirectWithSaving)
    {
        $this->redirectWithSaving = $redirectWithSaving;
    }

    /**
     * @return bool
     */
    public function isRedirectWithActivation()
    {
        return $this->redirectWithActivation;
    }

    /**
     * @param bool $redirectWithActivation
     */
    public function setRedirectWithActivation($redirectWithActivation)
    {
        $this->redirectWithActivation = $redirectWithActivation;
    }

    /**
     * @return boolean
     */
    public function isWithoutGuiHeader()
    {
        return $this->withoutGuiHeader;
    }

    /**
     * @param boolean $withoutGuiHeader
     */
    public function setWithoutGuiHeader($withoutGuiHeader)
    {
        $this->withoutGuiHeader = $withoutGuiHeader;
    }

    /**
     * @return boolean
     */
    public function isSaveWithoutMessages()
    {
        return $this->saveWithoutMessages;
    }

    /**
     * @param boolean $saveWithoutWithoutMessages
     */
    public function setSaveWithoutMessages($saveWithoutMessages)
    {
        $this->saveWithoutMessages = $saveWithoutMessages;
    }

    /**
     * @return boolean
     */
    public function isSaveWithoutSavingMessage()
    {
        return $this->saveWithoutSavingMessage;
    }

    /**
     * @param boolean $saveWithoutSavingMessage
     */
    public function setSaveWithoutSavingMessage($saveWithoutSavingMessage)
    {
        $this->saveWithoutSavingMessage = $saveWithoutSavingMessage;
    }

    /**
     * @return boolean
     */
    public function isWithInitialSaving()
    {
        return $this->withInitialSaving;
    }

    /**
     * @param boolean $withInitialSaving
     */
    public function setWithInitialSaving($withInitialSaving)
    {
        $this->withInitialSaving = $withInitialSaving;
    }

    /**
     * @return null
     */
    public function getRedirectSite()
    {
        return $this->redirectSite;
    }

    /**
     * @param null $redirectSite
     */
    public function setRedirectSite($redirectSite)
    {
        $this->redirectSite = $redirectSite;
    }

    /**
     * @return null
     */
    public function getRedirectBackSite()
    {
        return $this->redirectBackSite;
    }

    /**
     * @param null $redirectBackSite
     */
    public function setRedirectBackSite($redirectBackSite)
    {
        $this->redirectBackSite = $redirectBackSite;
    }

    /**
     * @return string
     */
    public function getBrickKey()
    {
        return $this->brickKey;
    }

    /**
     * @param string $brickKey
     */
    public function setBrickKey($brickKey)
    {
        $this->brickKey = $brickKey;
    }

    /**
     * @return string
     */
    public function getProjectKey()
    {
        return $this->projectKey;
    }

    /**
     * @param string $projectKey
     */
    public function setProjectKey($projectKey)
    {
        $this->projectKey = $projectKey;
    }

    /**
     * @return string
     */
    public function getBrickCaption()
    {
        return $this->brickCaption;
    }

    /**
     * @return string
     */
    public function getBrickCaptionPlural()
    {
        return $this->brickCaptionPlural;
    }

    /**
     * @param string $brickCaption
     */
    public function setBrickCaption($brickCaption)
    {
        $this->brickCaption = $brickCaption;
    }

    /**
     * @param string $brickCaptionPlural
     */
    public function setBrickCaptionPlural($brickCaptionPlural)
    {
        $this->brickCaptionPlural = $brickCaptionPlural;
    }

    /**
     * @return string
     */
    public function getCaptionField()
    {
        return $this->captionField;
    }

    /**
     * @param string $captionField
     */
    public function setCaptionField($captionField)
    {
        $this->captionField = $captionField;
    }

    /**
     * @return string
     */
    public function getParentModel()
    {
        return $this->parentModel;
    }

    /**
     * @param string $parentModel
     */
    public function setParentModel($parentModel)
    {
        $this->parentModel = $parentModel;
    }

    /**
     * @return string
     */
    public function getParentCaption()
    {
        return $this->parentCaption;
    }

    /**
     * @param string $parentCaption
     */
    public function setParentCaption($parentCaption)
    {
        $this->parentCaption = $parentCaption;
    }

    /**
     * @return string
     */
    public function getParentCaptionPlural()
    {
        return $this->parentCaptionPlural;
    }

    /**
     * @param string $parentCaptionPlural
     */
    public function setParentCaptionPlural($parentCaptionPlural)
    {
        $this->parentCaptionPlural = $parentCaptionPlural;
    }

    /**
     * @return boolean
     */
    public function getC4gMap()
    {
        return $this->c4gMap;
    }

    /**
     * @param boolean $c4gMap
     */
    public function setC4gMap($c4gMap)
    {
        $this->c4gMap = $c4gMap;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param string $contentId
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;
    }


    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param string $headline
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;
    }

    /**
     * @return null
     */
    public function getSendEMails()
    {
        return $this->sendEMails;
    }

    /**
     * @param null $sendEMails
     */
    public function setSendEMails($sendEMails)
    {
        $this->sendEMails = $sendEMails;
    }

    /**
     * @return null
     */
    public function getNotificationTypeContactRequest()
    {
        return $this->notificationTypeContactRequest;
    }

    /**
     * @param null $notificationTypeContactRequest
     */
    public function setNotificationTypeContactRequest($notificationTypeContactRequest)
    {
        $this->notificationTypeContactRequest = $notificationTypeContactRequest;
    }

    /**
     * @return null
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * @param null $notificationType
     */
    public function setNotificationType($notificationType)
    {
        $this->notificationType = $notificationType;
    }

    /**
     * @return boolean
     */
    public function isWithNotification()
    {
        return $this->withNotification;
    }

    /**
     * @param boolean $withNotification
     */
    public function setWithNotification($withNotification)
    {
        $this->withNotification = $withNotification;
    }

    /**
     * @return boolean
     */
    public function isWithBackup()
    {
        return $this->withBackup;
    }

    /**
     * @param boolean $withBackup
     */
    public function setWithBackup($withBackup)
    {
        $this->withBackup = $withBackup;
    }

    /**
     * @return boolean
     */
    public function isPopup()
    {
        return $this->popup;
    }

    /**
     * @param boolean $popup
     */
    public function setPopup($popup)
    {
        $this->popup = $popup;
    }

    /**
     * @return boolean
     */
    public function isWithActivationInfo()
    {
        return $this->withActivationInfo;
    }

    /**
     * @param boolean $withActivationInfo
     */
    public function setWithActivationInfo($withActivationInfo)
    {
        $this->withActivationInfo = $withActivationInfo;
    }

    /**
     * @return null
     */
    public function getModelListFunction()
    {
        return $this->modelListFunction;
    }

    /**
     * @param null $modelListFunction
     */
    public function setModelListFunction($modelListFunction)
    {
        $this->modelListFunction = $modelListFunction;
    }

    /**
     * @return boolean
     */
    public function isWithLabels()
    {
        return $this->withLabels;
    }

    /**
     * @param boolean $withLabels
     */
    public function setWithLabels($withLabels)
    {
        $this->withLabels = $withLabels;
    }

    /**
     * @return bool
     */
    public function isWithDescriptions()
    {
        return $this->withDescriptions;
    }

    /**
     * @param bool $withDescriptions
     */
    public function setWithDescriptions($withDescriptions)
    {
        $this->withDescriptions = $withDescriptions;
    }

    /**
     * @return int
     */
    public function getAdditionalId()
    {
        return $this->additionalId;
    }

    /**
     * @param int $additionalId
     */
    public function setAdditionalId($additionalId)
    {
        $this->additionalId = $additionalId;
    }

    /**
     * @return string
     */
    public function getAdditionalIdField()
    {
        return $this->additionalIdField;
    }

    /**
     * @param string $additionalIdField
     */
    public function setAdditionalIdField($additionalIdField)
    {
        $this->additionalIdField = $additionalIdField;
    }

    /**
     * @return string
     */
    public function getUniqueMessage()
    {
        return $this->uniqueMessage;
    }

    /**
     * @param string $uniqueResultText
     */
    public function setUniqueMessage($uniqueMessage)
    {
        $this->uniqueMessage = $uniqueMessage;
    }

    /**
     * @return string
     */
    public function getUniqueTitle()
    {
        return $this->uniqueTitle;
    }

    /**
     * @param string $uniqueTitle
     */
    public function setUniqueTitle($uniqueTitle)
    {
        $this->uniqueTitle = $uniqueTitle;
    }

    /**
     * @return bool
     */
    public function isWithPrintButton()
    {
        return $this->withPrintButton;
    }

    /**
     * @param bool $withPrintButton
     */
    public function setWithPrintButton($withPrintButton)
    {
        $this->withPrintButton = $withPrintButton;
    }

    /**
     * @return bool
     */
    public function isTabContent()
    {
        return $this->tabContent;
    }

    /**
     * @param bool $tabContent
     */
    public function setTabContent($tabContent)
    {
        $this->tabContent = $tabContent;
    }

    /**
     * @return int
     */
    public function getTabContentCounter()
    {
        return $this->tabContent_counter;
    }

    /**
     * @param int $tabContent_counter
     */
    public function setTabContentCounter($tabContent_counter)
    {
        $this->tabContent_counter = $tabContent_counter;
    }

    /**
     * @return bool
     */
    public function isWithTabContentCheck()
    {
        return $this->withTabContentCheck;
    }

    /**
     * @param bool $withTabContentCheck
     */
    public function setWithTabContentCheck($withTabContentCheck)
    {
        $this->withTabContentCheck = $withTabContentCheck;
    }

    /**
     * @return null
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }

    /**
     * @param null $filterParams
     */
    public function setFilterParams($filterParams)
    {
        $this->filterParams = $filterParams;
    }

    /**
     * @return bool
     */
    public function isConfirmActivation()
    {
        return $this->confirmActivation;
    }

    /**
     * @param bool $confirmActivation
     */
    public function setConfirmActivation($confirmActivation)
    {
        $this->confirmActivation = $confirmActivation;
    }

    /**
     * @return bool
     */
    public function isNotifyOnChanges()
    {
        return $this->notifyOnChanges;
    }

    /**
     * @param bool $notifyOnChanges
     */
    public function setNotifyOnChanges($notifyOnChanges)
    {
        $this->notifyOnChanges = $notifyOnChanges;
    }

    /**
     * @return bool
     */
    public function isSaveWithoutClose()
    {
        return $this->saveWithoutClose;
    }

    /**
     * @param bool $saveWithoutClose
     */
    public function setSaveWithoutClose($saveWithoutClose)
    {
        $this->saveWithoutClose = $saveWithoutClose;
    }

    /**
     * @return bool
     */
    public function isWithNextPrevButtons()
    {
        return $this->withNextPrevButtons;
    }

    /**
     * @param bool $withNextPrevButtons
     */
    public function setWithNextPrevButtons($withNextPrevButtons)
    {
        $this->withNextPrevButtons = $withNextPrevButtons;
    }

    /**
     * @return string
     */
    public function getGroupKeyField()
    {
        return $this->groupKeyField;
    }

    /**
     * @param string $groupKeyField
     */
    public function setGroupKeyField($groupKeyField)
    {
        $this->groupKeyField = $groupKeyField;
    }

    /**
     * @return string
     */
    public function getOnloadScript()
    {
        return $this->onloadScript;
    }

    /**
     * @param string $onloadScript
     */
    public function setOnloadScript($onloadScript)
    {
        $this->onloadScript = $onloadScript;
    }

    /**
     * @return mixed
     */
    public function getParentCaptionFields()
    {
        return $this->parentCaptionFields;
    }

    /**
     * @param mixed $parentCaptionField
     */
    public function setParentCaptionFields($parentCaptionField)
    {
        $this->parentCaptionFields = $parentCaptionField;
    }

    /**
     * @return C4GBeforeDialogSave
     */
    public function getBeforeSaveAction()
    {
        return $this->beforeSaveAction;
    }

    /**
     * @param C4GBeforeDialogSave $beforeSaveAction
     */
    public function setBeforeSaveAction($beforeSaveAction)
    {
        $this->beforeSaveAction = $beforeSaveAction;
    }

    /**
     * @return string
     */
    public function getAdditionalHeadText()
    {
        return $this->additionalHeadText;
    }

    /**
     * @param string $additionalHeadText
     */
    public function setAdditionalHeadText($additionalHeadText)
    {
        $this->additionalHeadText = $additionalHeadText;
    }

    /**
     * @return bool
     */
    public function isWithEmptyParentOption()
    {
        return $this->isWithEmptyParentOption;
    }

    /**
     * @param bool $isWithEmptyParentOption
     */
    public function setIsWithEmptyParentOption($isWithEmptyParentOption)
    {
        $this->isWithEmptyParentOption = $isWithEmptyParentOption;
    }

    /**
     * @return array
     */
    public function getParentCaptionCallback()
    {
        return $this->parentCaptionCallback;
    }

    /**
     * @param array $parentCaptionCallback
     */
    public function setParentCaptionCallback($parentCaptionCallback)
    {
        $this->parentCaptionCallback = $parentCaptionCallback;
    }

    /**
     * @return array
     */
    public function getRedirects()
    {
        return $this->redirects;
    }

    /**
     * @param array $redirects
     */
    public function setRedirects($redirects)
    {
        $this->redirects = $redirects;
    }

    public function addRedirect($newRedirect) {
        if ($newRedirect) {
            $addRedirect = true;
            foreach ($this->redirects as $redirect) {
                if ($newRedirect->getType() == $redirect->getType()) {
                    $addRedirect = false;
                    break;
                }
            }

            if ($addRedirect) {
                $this->redirects[] = $newRedirect;
            }
        }
    }

    /**
     * @return bool
     */
    public function isSaveWithUuid()
    {
        return $this->saveWithUuid;
    }

    /**
     * @param bool $saveWithUuid
     */
    public function setSaveWithUuid($saveWithUuid)
    {
        $this->saveWithUuid = $saveWithUuid;
    }

    /**
     * @return bool
     */
    public function isWithCommonParentOption()
    {
        return $this->isWithCommonParentOption;
    }

    /**
     * @param bool $isWithCommonParentOption
     */
    public function setIsWithCommonParentOption($isWithCommonParentOption)
    {
        $this->isWithCommonParentOption = $isWithCommonParentOption;
    }

    /**
     * @return bool
     */
    public function isSaveTimestamp()
    {
        return $this->saveTimestamp;
    }

    /**
     * @param bool $saveTimestamp
     */
    public function setSaveTimestamp($saveTimestamp)
    {
        $this->saveTimestamp = $saveTimestamp;
    }


    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->Uuid;
    }

    /**
     * @param string $Uuid
     */
    public function setUuid($Uuid)
    {
        $this->Uuid = $Uuid;
    }
}