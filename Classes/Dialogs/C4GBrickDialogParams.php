<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\CoreBundle\Classes\Callback\C4GCallback;
use con4gis\ProjectsBundle\Classes\Buttons\C4GBrickButton;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Conditions\C4GBrickCondition;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;
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
    private $captionField = 'caption'; //Feldname für die Benennung eines einzelnen Datensatzes. Kann überschrieben werden.
    private $brickCaption = ''; //Standard Singular Datensatzbeschriftung. Kann überschrieben werden.
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
    private $parentCaptionFields = []; // Für die Selectbox in der Parentauswahl, sodass die Bezeichnung auch aus einem anderen Feld als caption/name kommen kann
    private $parentCaptionCallback = []; // Funktion aus dem aktuellen Modul, sodass die Bezeichnungen über einen Callback gesetzt werden
    private $homeDir = ''; //homeDir for saving project data
    private $viewType = ''; //viewType -> see C4GBrickView
    private $viewParams = null; //viewParams for BrickView (params for List and Dialog)
    private $frozen = false; //special locks the dialog
    private $buttons = []; //dialog buttons
    private $accordion = false; //activates accordion (every headline is an accordeon button)
    private $accordion_counter = 0; //counts the accordions button to calc end of div
    private $accordion_all_opened = false; //opens all accordion buttons by default
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
    private $headlineTag = 'h1'; //HTML tag the headline is in
    private $withInitialSaving = false; //initial saving by dialog create
    private $redirectSite = null; //site to redirect (redirect button)
    private $redirectBackSite = null; //site to redirect by pushing back button
    private $c4gMap = false; //map content element
    private $contentId = ''; //special content element id
    private $sendEMails = null; //siehe C4GBrickSendEMail
    private $notificationType = null; //notification type
    private $notificationTypeContactRequest = null;
    private $withNotification = false; //activate notifications
    private $withBackup = false; //backup see con4gis-Streamer
    private $popup = false; //shows dialog as magnific popup
    private $withActivationInfo = false; //activation info
    private $modelListFunction = null; //Lädt die Datensätze der Tabelle über eine spezielle Modelfunktion.
    private $withLabels = true; //deactivates all labels
    private $withDescriptions = true; //deactivates all descriptions
    private $tableRows = false; //shows label and input in one row
    private $uniqueTitle = ''; //unique message title
    private $uniqueMessage = ''; //unique message
    private $withPrintButton = false; //activates print button
    private $savePrintoutToField = ''; //fieldname for automaticly document saving
    private $generatePrintoutWithSaving = false; //see savePrintoutToField
    private $printConditionField = ''; //only show print button if set
    private $passwordField = ''; //password to encrypt pdf document
    private $passwordFormat = ''; //useful with dateformat
    private $noPasswordOnButtonClick = false; //encrypt the document only on saving
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
    private $redirects = [];//C4GBrickRedirect
    private $saveWithUuid = false;
    private $saveTimestamp = true;
    private $uuid = '';
    private $modelDialogFunction = '';  //Model function to load the data for the dialog from the database. The function takes the dialog id as a parameter.
    private $selectParentCaption = '';
    private $selectParentMessage = '';
    private $saveInNewDataset = false;
    private $originalIdName = '';
    private $saveInNewDataSetIfCondition = null;
    private $doNotSaveIfValuesDidNotChange = false;     //Will not save to the database if the values did not change, but will not interrupt saving of sub dialogs.
    private $saveCallback = null;
    private $deleteCallback = null;
    private $showSuccessfullySavedMessage = true;
    private $hideChangesMessage = false;
    private $insertNewCondition = null;
    private $customDialogCallback = null;
    private $confirmActivationActionCallback = [];
    private $emptyListMessage = [];
    private $showCloseDialogPrompt = false; // Always show a confirmation dialog on close, even if nothing has been changed

    /**
     * C4GBrickDialogParams constructor.
     */
    public function __construct($brickKey, $viewType)
    {
        $this->brickKey = $brickKey;
        $this->viewType = $viewType;

        if (!$this->viewParams) {
            $this->viewParams = new C4GBrickViewParams($this->viewType);
        }

        $this->brickCaption = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BRICK_CAPTION'];
        $this->brickCaptionPlural = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['BRICK_CAPTION_PLURAL'];

        $this->setButtons($this->getDefaultDialogButtons($this->getViewType()));
    }

    /**
     * @return boolean
     */
    public function getDefaultDialogButtons($viewType)
    {
        $buttons = [];

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

            if (($viewType != C4GBrickViewType::GROUPFORM) &&
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
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * @param $memberId
     * @return $this
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;

        return $this;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param $projectId
     * @return $this
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * @return string
     */
    public function getProjectUuid()
    {
        return $this->projectUuid;
    }

    /**
     * @param $projectUuid
     * @return $this
     */
    public function setProjectUuid($projectUuid)
    {
        $this->projectUuid = $projectUuid;

        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentIdField()
    {
        return $this->parentIdField;
    }

    /**
     * @param $parentIdField
     * @return $this
     */
    public function setParentIdField($parentIdField)
    {
        $this->parentIdField = $parentIdField;

        return $this;
    }

    /**
     * @return string
     */
    public function getHomeDir()
    {
        return $this->homeDir;
    }

    /**
     * @param $homeDir
     * @return $this
     */
    public function setHomeDir($homeDir)
    {
        $this->homeDir = $homeDir;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewType()
    {
        return $this->viewType;
    }

    /**
     * @param $viewType
     * @return $this
     */
    public function setViewType($viewType)
    {
        $this->viewType = $viewType;

        return $this;
    }

    /**
     * @return null
     */
    public function getViewParams()
    {
        return $this->viewParams;
    }

    /**
     * @param $viewParams
     * @return $this
     */
    public function setViewParams($viewParams)
    {
        $this->viewParams = $viewParams;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isFrozen()
    {
        return $this->frozen;
    }

    /**
     * @param bool $frozen
     * @return $this
     */
    public function setFrozen($frozen = true)
    {
        $this->frozen = $frozen;

        return $this;
    }

    public function addButton(
        $type,
        $caption = '',
        $visible = true,
        $enabled = true,
        $action = '',
        $accesskey = '',
        $defaultByEnter = false,
        $notification = null,
        $condition = null,
        $additionalClass = ''
    ) {
        $exists = false;
        if ($caption == '') {
            $caption = C4GBrickButton::getTypeCaption($type);
        }

        if ($action == '') {
            $action = C4GBrickButton::getTypeAction($type);
        }

        $button = null;
        if ($type && ($type != C4GBrickConst::BUTTON_CLICK)) {
            foreach ($this->buttons as $btn) {
                if ($btn->getType() === $type) {
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
                $type,
                $caption,
                $visible,
                $enabled,
                $action,
                $accesskey,
                $defaultByEnter,
                $notification,
                $condition,
                $additionalClass
            );
            $this->buttons[] = $button;
        }

        return $button;
    }

    public function deleteButton($type)
    {
        $exists = false;
        foreach ($this->buttons as $btn) {
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

    public function changeButtonText($type, $caption)
    {
        foreach ($this->buttons as $btn) {
            if ($btn->getType() == $type) {
                $btn->setCaption($caption);

                return true;
            }
        }

        return false;
    }

    public function getButton($type)
    {
        foreach ($this->buttons as $button) {
            if ($button->getType() == $type) {
                return $button;
            }
        }

        return null;
    }

    public function getButtonsArray($type)
    {
        $result = [];
        foreach ($this->buttons as $button) {
            if ($button->getType() == $type) {
                $result[] = $button;
            }
        }

        return $result;
    }

    public function checkButtonVisibility($type, $dbValues = null)
    {
        if ($type) {
            foreach ($this->buttons as $button) {
                if ($button->getType() == $type) {
                    $condition = $button->getCondition();
                    if ($condition) {
                        $fieldName = $condition->getFieldName();
                        $value = $condition->getValue();

                        if ($dbValues && $dbValues->$fieldName) {
                            $button->setVisible($dbValues->$fieldName == $value);
                        } else {
                            $button->setVisible(false);
                        }
                    }

                    if ($button->isVisible()) {
                        return true;
                    }

                    return false;
                }
            }
        }

        return false;
    }

    public function removeAccordionIcons()
    {
        $this->addOnLoadScript('removeAccordionIcons();');
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param $buttons
     * @return $this
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isTableRows()
    {
        return $this->tableRows;
    }

    /**
     * @param bool $tableRows
     * @return $this
     */
    public function setTableRows($tableRows = true)
    {
        $this->tableRows = $tableRows;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAccordion()
    {
        return $this->accordion;
    }

    /**
     * @param bool $accordion
     * @return $this
     */
    public function setAccordion($accordion = true)
    {
        $this->accordion = $accordion;

        return $this;
    }

    /**
     * @return int
     */
    public function getAccordionCounter()
    {
        return $this->accordion_counter;
    }

    /**
     * @param $accordion_counter
     * @return $this
     */
    public function setAccordionCounter($accordion_counter)
    {
        $this->accordion_counter = $accordion_counter;

        return $this;
    }

    /**
     * @param bool $accordion_all_opened
     * @return mixed
     */
    public function setAccordionAllOpened($accordion_all_opened = true)
    {
        if ($accordion_all_opened == true) {
            return $this->addOnLoadScript('openAccordion("all");');
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSaveOnMandatory()
    {
        return $this->saveOnMandatory;
    }

    /**
     * @param bool $saveOnMandatory
     * @return $this
     */
    public function setSaveOnMandatory($saveOnMandatory = true)
    {
        $this->saveOnMandatory = $saveOnMandatory;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isMandatoryCheckOnActivate()
    {
        return $this->mandatoryCheckOnActivate;
    }

    /**
     * @param bool $mandatoryCheckOnActivate
     * @return $this
     */
    public function setMandatoryCheckOnActivate($mandatoryCheckOnActivate = true)
    {
        $this->mandatoryCheckOnActivate = $mandatoryCheckOnActivate;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isRedirectWithSaving()
    {
        return $this->redirectWithSaving;
    }

    /**
     * @param bool $redirectWithSaving
     * @return $this
     */
    public function setRedirectWithSaving($redirectWithSaving = true)
    {
        $this->redirectWithSaving = $redirectWithSaving;

        return $this;
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
     * @return $this
     */
    public function setRedirectWithActivation($redirectWithActivation = true)
    {
        $this->redirectWithActivation = $redirectWithActivation;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithoutGuiHeader()
    {
        return $this->withoutGuiHeader;
    }

    /**
     * @param bool $withoutGuiHeader
     * @return $this
     */
    public function setWithoutGuiHeader($withoutGuiHeader = true)
    {
        $this->withoutGuiHeader = $withoutGuiHeader;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSaveWithoutMessages()
    {
        return $this->saveWithoutMessages;
    }

    /**
     * @param bool $saveWithoutMessages
     * @return $this
     */
    public function setSaveWithoutMessages($saveWithoutMessages = true)
    {
        $this->saveWithoutMessages = $saveWithoutMessages;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSaveWithoutSavingMessage()
    {
        return $this->saveWithoutSavingMessage;
    }

    /**
     * @param bool $saveWithoutSavingMessage
     * @return $this
     */
    public function setSaveWithoutSavingMessage($saveWithoutSavingMessage = true)
    {
        $this->saveWithoutSavingMessage = $saveWithoutSavingMessage;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithInitialSaving()
    {
        return $this->withInitialSaving;
    }

    /**
     * @param bool $withInitialSaving
     * @return $this
     */
    public function setWithInitialSaving($withInitialSaving = true)
    {
        $this->withInitialSaving = $withInitialSaving;

        return $this;
    }

    /**
     * @return null
     */
    public function getRedirectSite()
    {
        return $this->redirectSite;
    }

    /**
     * @param $redirectSite
     * @return $this
     */
    public function setRedirectSite($redirectSite)
    {
        $this->redirectSite = $redirectSite;

        return $this;
    }

    /**
     * @return null
     */
    public function getRedirectBackSite()
    {
        return $this->redirectBackSite;
    }

    /**
     * @param $redirectBackSite
     * @return $this
     */
    public function setRedirectBackSite($redirectBackSite)
    {
        $this->redirectBackSite = $redirectBackSite;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrickKey()
    {
        return $this->brickKey;
    }

    /**
     * @param $brickKey
     * @return $this
     */
    public function setBrickKey($brickKey)
    {
        $this->brickKey = $brickKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getProjectKey()
    {
        return $this->projectKey;
    }

    /**
     * @param $projectKey
     * @return $this
     */
    public function setProjectKey($projectKey)
    {
        $this->projectKey = $projectKey;

        return $this;
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
     * @param $brickCaption
     * @return $this
     */
    public function setBrickCaption($brickCaption)
    {
        $this->brickCaption = $brickCaption;

        return $this;
    }

    /**
     * @param $brickCaptionPlural
     * @return $this
     */
    public function setBrickCaptionPlural($brickCaptionPlural)
    {
        $this->brickCaptionPlural = $brickCaptionPlural;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaptionField()
    {
        return $this->captionField;
    }

    /**
     * @param $captionField
     * @return $this
     */
    public function setCaptionField($captionField)
    {
        $this->captionField = $captionField;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentModel()
    {
        return $this->parentModel;
    }

    /**
     * @param $parentModel
     * @return $this
     */
    public function setParentModel($parentModel)
    {
        $this->parentModel = $parentModel;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentCaption()
    {
        return $this->parentCaption;
    }

    /**
     * @param $parentCaption
     * @return $this
     */
    public function setParentCaption($parentCaption)
    {
        $this->parentCaption = $parentCaption;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentCaptionPlural()
    {
        return $this->parentCaptionPlural;
    }

    /**
     * @param $parentCaptionPlural
     * @return $this
     */
    public function setParentCaptionPlural($parentCaptionPlural)
    {
        $this->parentCaptionPlural = $parentCaptionPlural;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getC4gMap()
    {
        return $this->c4gMap;
    }

    /**
     * @param $c4gMap
     * @return $this
     */
    public function setC4gMap($c4gMap)
    {
        $this->c4gMap = $c4gMap;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param $contentId
     * @return $this
     */
    public function setContentId($contentId)
    {
        $this->contentId = $contentId;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * @param $headline
     * @return $this
     */
    public function setHeadline($headline)
    {
        $this->headline = $headline;

        return $this;
    }

    /**
     * @return null
     */
    public function getSendEMails()
    {
        return $this->sendEMails;
    }

    /**
     * @param $sendEMails
     * @return $this
     */
    public function setSendEMails($sendEMails)
    {
        $this->sendEMails = $sendEMails;

        return $this;
    }

    /**
     * @return null
     */
    public function getNotificationTypeContactRequest()
    {
        return $this->notificationTypeContactRequest;
    }

    /**
     * @param $notificationTypeContactRequest
     * @return $this
     */
    public function setNotificationTypeContactRequest($notificationTypeContactRequest)
    {
        $this->notificationTypeContactRequest = $notificationTypeContactRequest;

        return $this;
    }

    /**
     * @return null
     */
    public function getNotificationType()
    {
        return $this->notificationType;
    }

    /**
     * @param $notificationType
     * @return $this
     */
    public function setNotificationType($notificationType)
    {
        $this->notificationType = $notificationType;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithNotification()
    {
        return $this->withNotification;
    }

    /**
     * @param $withNotification
     * @return $this
     */
    public function setWithNotification($withNotification = true)
    {
        $this->withNotification = $withNotification;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithBackup()
    {
        return $this->withBackup;
    }

    /**
     * @param $withBackup
     * @return $this
     */
    public function setWithBackup($withBackup)
    {
        $this->withBackup = $withBackup;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isPopup()
    {
        return $this->popup;
    }

    /**
     * @param bool $popup
     * @return $this
     */
    public function setPopup($popup = true)
    {
        $this->popup = $popup;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithActivationInfo()
    {
        return $this->withActivationInfo;
    }

    /**
     * @param bool $withActivationInfo
     * @return $this
     */
    public function setWithActivationInfo($withActivationInfo = true)
    {
        $this->withActivationInfo = $withActivationInfo;

        return $this;
    }

    /**
     * @return null
     */
    public function getModelListFunction()
    {
        return $this->modelListFunction;
    }

    /**
     * @param $modelListFunction
     * @return $this
     */
    public function setModelListFunction($modelListFunction)
    {
        $this->modelListFunction = $modelListFunction;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWithLabels()
    {
        return $this->withLabels;
    }

    /**
     * @param bool $withLabels
     * @return $this
     */
    public function setWithLabels($withLabels = true)
    {
        $this->withLabels = $withLabels;

        return $this;
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
     * @return $this
     */
    public function setWithDescriptions($withDescriptions = true)
    {
        $this->withDescriptions = $withDescriptions;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalId()
    {
        return $this->additionalId;
    }

    /**
     * @param $additionalId
     * @return $this
     */
    public function setAdditionalId($additionalId)
    {
        $this->additionalId = $additionalId;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalIdField()
    {
        return $this->additionalIdField;
    }

    /**
     * @param $additionalIdField
     * @return $this
     */
    public function setAdditionalIdField($additionalIdField)
    {
        $this->additionalIdField = $additionalIdField;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueMessage()
    {
        return $this->uniqueMessage;
    }

    /**
     * @param $uniqueMessage
     * @return $this
     */
    public function setUniqueMessage($uniqueMessage)
    {
        $this->uniqueMessage = $uniqueMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getUniqueTitle()
    {
        return $this->uniqueTitle;
    }

    /**
     * @param $uniqueTitle
     * @return $this
     */
    public function setUniqueTitle($uniqueTitle)
    {
        $this->uniqueTitle = $uniqueTitle;

        return $this;
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
     * @return $this
     */
    public function setWithPrintButton($withPrintButton = true)
    {
        $this->withPrintButton = $withPrintButton;

        return $this;
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
     * @return $this
     */
    public function setTabContent($tabContent = true)
    {
        $this->tabContent = $tabContent;

        return $this;
    }

    /**
     * @return int
     */
    public function getTabContentCounter()
    {
        return $this->tabContent_counter;
    }

    /**
     * @param $tabContent_counter
     * @return $this
     */
    public function setTabContentCounter($tabContent_counter)
    {
        $this->tabContent_counter = $tabContent_counter;

        return $this;
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
     * @return $this
     */
    public function setWithTabContentCheck($withTabContentCheck = true)
    {
        $this->withTabContentCheck = $withTabContentCheck;

        return $this;
    }

    /**
     * @return null
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }

    /**
     * @param $filterParams
     * @return $this
     */
    public function setFilterParams($filterParams)
    {
        $this->filterParams = $filterParams;

        return $this;
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
     * @return $this
     */
    public function setConfirmActivation($confirmActivation = true)
    {
        $this->confirmActivation = $confirmActivation;

        return $this;
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
     * @return $this
     */
    public function setNotifyOnChanges($notifyOnChanges = true)
    {
        $this->notifyOnChanges = $notifyOnChanges;

        return $this;
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
     * @return $this
     */
    public function setSaveWithoutClose($saveWithoutClose = true)
    {
        $this->saveWithoutClose = $saveWithoutClose;

        return $this;
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
     * @return $this
     */
    public function setWithNextPrevButtons($withNextPrevButtons = true)
    {
        $this->withNextPrevButtons = $withNextPrevButtons;

        return $this;
    }

    /**
     * @return string
     */
    public function getGroupKeyField()
    {
        return $this->groupKeyField;
    }

    /**
     * @param $groupKeyField
     * @return $this
     */
    public function setGroupKeyField($groupKeyField)
    {
        $this->groupKeyField = $groupKeyField;

        return $this;
    }

    /**
     * @return string
     */
    public function getOnloadScript()
    {
        return $this->onloadScript;
    }

    /**
     * Calls the adder to remain backwards compatible.
     * @param $onloadScript
     * @return $this
     */
    public function setOnloadScript($onloadScript)
    {
        return $this->addOnLoadScript($onloadScript);
    }

    /**
     * @param $onloadScript
     * @return $this
     */
    public function addOnLoadScript($onloadScript)
    {
        if ($this->onloadScript !== '') {
            $this->onloadScript .= $onloadScript;
        } else {
            $this->onloadScript = $onloadScript;
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParentCaptionFields()
    {
        return $this->parentCaptionFields;
    }

    /**
     * @param $parentCaptionField
     * @return $this
     */
    public function setParentCaptionFields($parentCaptionField)
    {
        $this->parentCaptionFields = $parentCaptionField;

        return $this;
    }

    /**
     * @return C4GBeforeDialogSave
     */
    public function getBeforeSaveAction()
    {
        return $this->beforeSaveAction;
    }

    /**
     * @param $beforeSaveAction
     * @return $this
     */
    public function setBeforeSaveAction($beforeSaveAction)
    {
        $this->beforeSaveAction = $beforeSaveAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalHeadText()
    {
        return $this->additionalHeadText;
    }

    /**
     * @param $additionalHeadText
     * @return $this
     */
    public function setAdditionalHeadText($additionalHeadText)
    {
        $this->additionalHeadText = $additionalHeadText;

        return $this;
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
     * @return $this
     */
    public function setIsWithEmptyParentOption($isWithEmptyParentOption = true)
    {
        $this->isWithEmptyParentOption = $isWithEmptyParentOption;

        return $this;
    }

    /**
     * @return array
     */
    public function getParentCaptionCallback()
    {
        return $this->parentCaptionCallback;
    }

    /**
     * @param $parentCaptionCallback
     * @return $this
     */
    public function setParentCaptionCallback($parentCaptionCallback)
    {
        $this->parentCaptionCallback = $parentCaptionCallback;

        return $this;
    }

    /**
     * @return array
     */
    public function getRedirects()
    {
        return $this->redirects;
    }

    /**
     * @param $redirects
     * @return $this
     */
    public function setRedirects($redirects)
    {
        $this->redirects = $redirects;

        return $this;
    }

    public function addRedirect($newRedirect)
    {
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

        return $this;
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
     * @return $this
     */
    public function setSaveWithUuid($saveWithUuid = true)
    {
        $this->saveWithUuid = $saveWithUuid;

        return $this;
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
     * @return $this
     */
    public function setIsWithCommonParentOption($isWithCommonParentOption = true)
    {
        $this->isWithCommonParentOption = $isWithCommonParentOption;

        return $this;
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
     * @return $this
     */
    public function setSaveTimestamp($saveTimestamp = true)
    {
        $this->saveTimestamp = $saveTimestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @param $uuid
     * @return $this
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeadlineTag()
    {
        return $this->headlineTag;
    }

    /**
     * @param $headlineTag
     * @return $this
     */
    public function setHeadlineTag($headlineTag)
    {
        $this->headlineTag = $headlineTag;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelDialogFunction()
    {
        return $this->modelDialogFunction;
    }

    /**
     * @param string $modelDialogFunction
     * @return C4GBrickDialogParams
     */
    public function setModelDialogFunction($modelDialogFunction)
    {
        $this->modelDialogFunction = $modelDialogFunction;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectParentCaption(): string
    {
        return $this->selectParentCaption;
    }

    /**
     * @param string $selectParentCaption
     * @return C4GBrickDialogParams
     */
    public function setSelectParentCaption(string $selectParentCaption): C4GBrickDialogParams
    {
        $this->selectParentCaption = $selectParentCaption;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectParentMessage(): string
    {
        return $this->selectParentMessage;
    }

    /**
     * @param string $selectParentMessage
     * @return C4GBrickDialogParams
     */
    public function setSelectParentMessage(string $selectParentMessage): C4GBrickDialogParams
    {
        $this->selectParentMessage = $selectParentMessage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSaveInNewDataset(): bool
    {
        return $this->saveInNewDataset;
    }

    /**
     * @param bool $saveInNewDataset
     * @return C4GBrickDialogParams
     */
    public function setSaveInNewDataset(bool $saveInNewDataset = true): C4GBrickDialogParams
    {
        $this->saveInNewDataset = $saveInNewDataset;

        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalIdName(): string
    {
        return $this->originalIdName;
    }

    /**
     * @param string $originalIdName
     * @return C4GBrickDialogParams
     */
    public function setOriginalIdName(string $originalIdName): C4GBrickDialogParams
    {
        $this->originalIdName = $originalIdName;

        return $this;
    }

    /**
     * @return C4GBrickCondition
     */
    public function getSaveInNewDataSetIfCondition(): ?C4GBrickCondition
    {
        return $this->saveInNewDataSetIfCondition;
    }

    /**
     * @param C4GBrickCondition $saveInNewDataSetIfCondition
     * @return $this
     */
    public function setSaveInNewDataSetIfCondition(C4GBrickCondition $saveInNewDataSetIfCondition = null)
    {
        $this->saveInNewDataSetIfCondition = $saveInNewDataSetIfCondition;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDoNotSaveIfValuesDidNotChange(): bool
    {
        return $this->doNotSaveIfValuesDidNotChange;
    }

    /**
     * @param bool $doNotSaveIfValuesDidNotChange
     * @return C4GBrickDialogParams
     */
    public function setDoNotSaveIfValuesDidNotChange(bool $doNotSaveIfValuesDidNotChange): C4GBrickDialogParams
    {
        $this->doNotSaveIfValuesDidNotChange = $doNotSaveIfValuesDidNotChange;

        return $this;
    }

    /**
     * @return C4GCallback
     */
    public function getSaveCallback(): ?C4GCallback
    {
        return $this->saveCallback;
    }

    /**
     * @param C4GCallback $saveCallback
     * @return C4GBrickDialogParams
     */
    public function setSaveCallback(C4GCallback $saveCallback): C4GBrickDialogParams
    {
        $this->saveCallback = $saveCallback;

        return $this;
    }

    /**
     * @return C4GCallback
     */
    public function getDeleteCallback(): C4GCallback
    {
        return $this->deleteCallback;
    }

    /**
     * @param C4GCallback $deleteCallback
     * @return C4GBrickDialogParams
     */
    public function setDeleteCallback(C4GCallback $deleteCallback): C4GBrickDialogParams
    {
        $this->deleteCallback = $deleteCallback;

        return $this;
    }

    /**
     * @return bool
     */
    public function isShowSuccessfullySavedMessage(): bool
    {
        return $this->showSuccessfullySavedMessage;
    }

    /**
     * @param bool $showSuccessfullySavedMessage
     * @return C4GBrickDialogParams
     */
    public function setShowSuccessfullySavedMessage(bool $showSuccessfullySavedMessage): C4GBrickDialogParams
    {
        $this->showSuccessfullySavedMessage = $showSuccessfullySavedMessage;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHideChangesMessage(): bool
    {
        return $this->hideChangesMessage;
    }

    /**
     * @param bool $hideChangesMessage
     */
    public function setHideChangesMessage(bool $hideChangesMessage = true)
    {
        $this->hideChangesMessage = $hideChangesMessage;
    }

    /**
     * @return C4GCallback
     */
    public function getInsertNewCondition(): ?C4GCallback
    {
        return $this->insertNewCondition;
    }

    /**
     * @param C4GCallback $callback
     * @return C4GBrickDialogParams
     */
    public function setInsertNewCondition(C4GCallback $callback): C4GBrickDialogParams
    {
        $this->insertNewCondition = $callback;

        return $this;
    }

    public function clearInsertNewCondition()
    {
        $this->insertNewCondition = null;
    }

    /**
     * @return C4GCallback|null
     */
    public function getCustomDialogCallback(): ?C4GCallback
    {
        return $this->customDialogCallback;
    }

    /**
     * @param C4GCallback $customDialogCallback
     * @return $this
     */
    public function setCustomDialogCallback(C4GCallback $customDialogCallback)
    {
        $this->customDialogCallback = $customDialogCallback;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfirmActivationActionCallback()
    {
        return $this->confirmActivationActionCallback;
    }

    /**
     * @param array $confirmActivationActionCallback
     */
    public function setConfirmActivationActionCallback(array $confirmActivationActionCallback)
    {
        $this->confirmActivationActionCallback = $confirmActivationActionCallback;
    }

    /**
     * @return array
     */
    public function getEmptyListMessage()
    {
        return $this->emptyListMessage;
    }

    /**
     * @param $title
     * @param $message
     * @return array
     */
    public function setEmptyListMessage($title, $message)
    {
        $this->emptyListMessage = [$title, $message];

        return $this->emptyListMessage;
    }

    /**
     * @return string
     */
    public function getSavePrintoutToField(): string
    {
        return $this->savePrintoutToField;
    }

    /**
     * @param string $savePrintoutToField
     */
    public function setSavePrintoutToField(string $savePrintoutToField): void
    {
        $this->savePrintoutToField = $savePrintoutToField;
    }

    /**
     * @return bool
     */
    public function isGeneratePrintoutWithSaving(): bool
    {
        return $this->generatePrintoutWithSaving;
    }

    /**
     * @param bool $generatePrintoutWithSaving
     */
    public function setGeneratePrintoutWithSaving(bool $generatePrintoutWithSaving): void
    {
        $this->generatePrintoutWithSaving = $generatePrintoutWithSaving;
    }

    /**
     * @return string
     */
    public function getPasswordField(): string
    {
        return $this->passwordField;
    }

    /**
     * @param string $passwordField
     */
    public function setPasswordField(string $passwordField): void
    {
        $this->passwordField = $passwordField;
    }

    /**
     * @return string
     */
    public function getPasswordFormat(): string
    {
        return $this->passwordFormat;
    }

    /**
     * @param string $passwordFormat
     */
    public function setPasswordFormat(string $passwordFormat): void
    {
        $this->passwordFormat = $passwordFormat;
    }

    /**
     * @return bool
     */
    public function isNoPasswordOnButtonClick(): bool
    {
        return $this->noPassordOnButtonClick;
    }

    /**
     * @param bool $noPassordOnButtonClick
     */
    public function setNoPasswordOnButtonClick(bool $noPassordOnButtonClick): void
    {
        $this->noPassordOnButtonClick = $noPassordOnButtonClick;
    }

    /**
     * @return string
     */
    public function getPrintConditionField(): string
    {
        return $this->printConditionField;
    }

    /**
     * @param string $printConditionField
     */
    public function setPrintConditionField(string $printConditionField): void
    {
        $this->printConditionField = $printConditionField;
    }

    /**
     * @return bool
     */
    public function isShowCloseDialogPrompt(): bool
    {
        return $this->showCloseDialogPrompt;
    }

    /**
     * @param bool $showCloseDialogPrompt
     */
    public function setShowCloseDialogPrompt(bool $showCloseDialogPrompt = true): void
    {
        $this->showCloseDialogPrompt = $showCloseDialogPrompt;
    }
}
