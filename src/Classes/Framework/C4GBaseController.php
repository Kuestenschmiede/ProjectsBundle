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
namespace con4gis\ProjectsBundle\Classes\Framework;

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\CoreBundle\Classes\C4GVersionProvider;
use con4gis\CoreBundle\Classes\ResourceLoader;
use con4gis\GroupsBundle\Resources\contao\models\MemberGroupModel;
use con4gis\GroupsBundle\Resources\contao\models\MemberModel;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickAction;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabase;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseParams;
use con4gis\ProjectsBundle\Classes\Database\C4GBrickDatabaseType;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GDialogChangeHandler;
use con4gis\ProjectsBundle\Classes\jQuery\C4GJQueryGUI;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickListParams;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Notifications\C4GBrickNotification;
use con4gis\ProjectsBundle\Classes\Permission\C4GTablePermission;
use con4gis\ProjectsBundle\Classes\Session\C4gBrickSession;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use Contao\ArrayUtil;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Terminal42\NotificationCenterBundle\NotificationCenter;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Contao\Environment;
use Contao\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\Input;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Composer\InstalledVersions;

/**
 * Class C4GBaseController
 *
 * The brick module parent is needed by all con4gis-Projects based fe modules.
 *
 * @package c4g\projects
 */

class C4GBaseController extends AbstractFrontendModuleController
{
    //mandatory params
    protected $brickKey = ''; //unique string key for module request and con4gis-Groups rights.
    protected $viewType = C4GBrickViewType::GROUPBASED; //see C4GBrickViewType
    protected $publicViewType = ''; //automatic switch from based to view type
    protected $tableName = ''; //needed by default DatabaseType
    protected $findBy = []; //qualify default dataset
    protected $modelClass = ''; //needed by default DatabaseType
    protected $fieldList = null; //fieldlist filled by module class with objects inherited by C4GBrickField
    protected $initialFieldList = null; //initial fieldlist used for history lookups when the fieldlist is changed
    protected $languageFile = ''; //loading one language file for yout module class

    //optional and modifiable params

    //embedding additional sources
    protected $strTemplate = 'mod_c4g_brick'; //default module template
    protected $printTemplate = 'pdf_c4g_brick'; //default print template
    protected $uiTheme = ''; //loading your own ui theme for your module class (https://jqueryui.com/themeroller/)

    //group params
    protected $group_id = -1; //the current group id for group-
    protected $ignoreCon4GisRights = false; //Press true to use Contao rights only

    //project params
    protected $project_id = -1; //the current project id for projectbased modules.
    protected $project_uuid = null; //the current project uuid for projectbased im- and export.
    protected $projectKey = ''; //needed to control project based modules.

    //parent params
    protected $parent_id = ''; //the current parent id for parentbased modules.
    protected $parentCaption = ''; //hint caption for parent context messages.
    protected $parentCaptionPlural = ''; //hint plural caption for parent context messages.
    protected $parentModel = ''; //needed with ViewType PROJECTPARENTBASED and GROUPPARENTVIEW.
    protected $parentIdField = ''; //needed if not default pid field.

    //doctrine params
    protected $databaseType = C4GBrickDatabaseType::DCA_MODEL; //see C4gBrickDatabaseType
    protected $entityClass = '';

    //con4gis global settings
    protected $settings = null; //tl_c4g_settings

    //caption params
    protected $brickCaption = ''; //default singular dataset caption
    protected $brickCaptionPlural = ''; //default plural dataset caption
    protected $captionField = 'caption'; //caption field for singular dataset caption needed for logging (logbook)

    //global objects
    protected $brickDatabase = null; //brick database functions
    protected $listParams = null; //list based params (needed for list and tiles -> see C4GBrickListParams)
    protected $dialogParams = null; //dialog based params (needed by dialogs -> see C4GBrickDialogParams)
    protected $dialogChangeHandler = null;
    protected $putVars = null; //default dialog values
    protected $viewParams = null; //view based params (needed for list, tiles and dialogs -> see C4gBrickViewParams)

    //matching params
    protected $searchTable = null; //searching in tablename -> see C4GMatching

    //email params
    protected $sendEMails = null; //see -> C4GBrickSendEMail
    protected $withNotification = false; //using notification center

    //expert params
    protected $modelListFunction = null; //loading dataset by a special function
    protected $modelDialogFunction = null; //loading dataset by a special function
    protected $withBackup = false; //doing automaticly exports (backups)
    protected $withActivationInfo = false; //activation info
    protected $withLabels = true; //switching on/off all labels
    protected $isPopup = false; //needed with magnific popup
    protected $c4g_map = false; //needed for embedding con4gis maps
    protected $permalink_field = 'id'; //using another field for permalink (default: id field)
    protected $permalink_name = 'item'; //for setting an own get param
    protected $permalinkModelClass = null; //if table filled by modelListFunction
    protected $withPermissionCheck = true; // can be set to false to avoid the table permission check
    protected $renewInitialValues = false; // The initial values are reset during the reload.

    //UUID params
    protected $UUID = 'c4g_brick_uuid'; //Name of the uuid cookie in the browser. Can be overridden in child.
    protected $useUuidCookie = false; //Can be overridden in child to suppress the uuid cookie.

    protected $asnycList = false; // set true when the list should be loaded after the initial page load
    protected $language = '';

    //Resource Params
    protected $loadDefaultResources = true;
    protected $loadTrixEditorResources = false;
    protected $trixEditorResourceParams = [];
    protected $loadDatePicker = true;
    protected $loadDateTimePickerResources = false;
    protected $loadChosenResources = false;
    protected $loadClearBrowserUrlResources = false;
    protected $loadConditionalFieldDisplayResources = true;
    protected $loadMoreButtonResources = false;
    protected $loadFontAwesomeResources = false;
    protected $loadTriggerSearchFromOtherModuleResources = false;
    protected $loadFileUploadResources = false;
    protected $loadMultiColumnResources = false;
    protected $loadMiniSearchResources = false;
    protected $loadHistoryPushResources = false;
    protected $loadHistoryBrickPushResources = true;
    protected $loadSignaturePadResources = false;

    //JQuery GUI Resource Params
    protected $jQueryAddCore = true;
    protected $jQueryAddJquery = true;
    protected $jQueryAddJqueryUI = false;
    protected $jQueryUseTree = false;
    protected $jQueryUseTable = true;
    protected $jQueryUseHistory = false;
    protected $jQueryUseTooltip = true;
    protected $jQueryUseMaps = false;
    protected $jQueryUseGoogleMaps = false;
    protected $jQueryUseMapsEditor = false;
    protected $jQueryUseWswgEditor = false;
    protected $jQueryUseScrollPane = true;
    protected $jQueryUsePopups = false;

    //Scripts that can be overwritten or appended in the module for individual functions and layout
    protected $brickStyle = '';
    protected $printStyle = '';
    protected $brickScript = '';

    protected $rootDir;
    protected $session;
    protected $framework;
    protected $model;

    public function __construct(string $rootDir, RequestStack $requestStack, ContaoFramework $framework, ModuleModel $model = null)
    { 
        $session = $requestStack->getCurrentRequest()->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }

        $this->rootDir      = $rootDir;
        $this->framework    = $framework;
        $this->framework->initialize(true);

        $this->session = new C4gBrickSession($session);

        if ($model) {
            $this->model = $model;
            foreach ($model->row() as $fieldName=>$value) {
                if ($fieldName === 'headline') {
                    $headlineArray = StringUtil::deserialize($value);
                    $unit = $headlineArray['unit'];
                    $value = $headlineArray['value'];
                    if ($value) {
                        $this->headline = $value;
                        $this->headlineTag = $unit;
                    }
                } else if (strpos($value,'a:')) {
                    $this->$fieldName = StringUtil::deserialize($value);
                } else {
                    $this->$fieldName = $value;
                }
            }
        }
    }

    /**
     * @return C4gBrickSession
     */
    public function getSession(): C4gBrickSession
    {
        return $this->session;
    }


    /**
     * @param Template $template
     * @param ModuleModel $model
     * @param Request $request
     * @return Response|null
     */
    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $this->model = $model;

        foreach ($model->row() as $fieldName=>$value) {
            if ($fieldName === 'headline') {
                $headlineArray = StringUtil::deserialize($value);
                $unit = $headlineArray['unit'];
                $value = $headlineArray['value'];
                if ($value) {
                    $this->headline = $value;
                    $this->headlineTag = $unit;
                }
            } else if (!is_array($value) && strpos($value,'a:')) {
                $this->$fieldName = StringUtil::deserialize($value);
            } else {
                $this->$fieldName = $value;
            }
        }

        $this->compileJquery();

        $this->compileJavaScript();
        $this->compileCss();

        $classname = rawurlencode(get_class($this));

        $data['id'] = $this->id;
        $data['ajaxUrl'] = 'con4gis/brick_ajax_api/' . $GLOBALS['TL_LANGUAGE'] . '/' . $classname;
        $data['ajaxData'] = $this->id;
        $data['height'] = 'auto';
        $data['width'] = '100%';
        $data['embedDialogs'] = true;
        $data['jquiEmbeddedDialogs'] = $this->jQueryAddJqueryUI;
        $data['jquiBreadcrumb'] = $this->jQueryAddJqueryUI;
        $data['jquiButtons'] = $this->jQueryAddJqueryUI;

        if (($_SERVER['REQUEST_METHOD']) == 'PUT') {
            parse_str(file_get_contents('php://input'), $this->putVars);
        }

        if (key_exists('state', $_GET)) {
            $request = $_GET['state'];
        } /*else if ($this->permalink_name && key_exists($this->permalink_name, $_GET)) {
            $request = $_GET[$this->permalink_name];
        }*/ else {
            $request = 'initnav';
        }

        if ($request == 'undefined') {
            $request = C4GBrickActionType::IDENTIFIER_LIST . ':-1';
        }
        if ($this->asnycList && $request == 'initnav') {
            $arrAction = [];
            $arrAction['initAction'] = 'C4GShowListAction:-1';
            $data['initData'] = json_encode($arrAction);
        } else {
            $initData = $this->generateAjax($request);

            if ($initData && (is_array(json_decode($initData)) && (count(json_decode($initData)) > 0) ||
                    (json_decode($initData) instanceof \stdClass) && count((array) json_decode($initData)) > 0)) {
                $data['initData'] = $initData;
            } else {
                $initData = $this->generateAjax(C4GBrickActionType::IDENTIFIER_LIST . ':-1');
                $data['initData'] = $initData;
            }
        }

        $data['div'] = 'c4g_brick';

        $template->c4gData = $data;

        return $template->getResponse();
    }

    /**
     * @return array
     */
    public function addFields() : array
    {
        //to fill $this->fieldList in your module class
        return [];
    }

    /**
     * @return string
     */
    public function getCaptionField()
    {
        return $this->captionField;
    } //doctrine entity class

    /**
     * module class function after saving
     */
    public function afterSaveAction($changes, $insertId)
    {
        //to run code in your module class after saving
    }

    /**
     * module class function after closing
     */
    public function onShowListAction()
    {
        //to run code in your module class after closing
    }

    /**
     * @param $type
     */
    protected function getIdByType($type)
    {
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        switch ($type) {
            case C4GBrickConst::ID_TYPE_MEMBER:
                if ($hasFrontendUser) {
                    $user = FrontendUser::getInstance();
                    $user->authenticate();
                }

                return $user->id;
            case C4GBrickConst::ID_TYPE_GROUP:
                $group_id = $this->dialogParams->getGroupId();
                if (!$group_id || ($group_id <= 0)) {
                    $group_id = $this->session->getSessionValue('c4g_brick_group_id');
                } else {
                    $this->session->setSessionValue('c4g_brick_group_id',$group_id);
                }

                return $group_id;
            case C4GBrickConst::ID_TYPE_PROJECT:
                $project_id = $this->dialogParams->getProjectId();
                if (!$project_id || ($project_id <= 0)) {
                    $project_id = $this->session->getSessionValue('c4g_brick_project_id');
                } else {
                    $this->session->setSessionValue('c4g_brick_project_id',$project_id);
                }

                return $project_id;
            case C4GBrickConst::ID_TYPE_PARENT:
                $parent_id = $this->dialogParams->getParentId();
                if (!$parent_id || ($parent_id <= 0)) {
                    $parent_id = $this->session->getSessionValue('c4g_brick_parent_id');
                } else {
                    $this->session->setSessionValue('c4g_brick_parent_id',$parent_id);
                }

                return $parent_id;
            default:
                return false;
        }
    }

    private function groupCheck()
    {
        $user = FrontendUser::getInstance();
        //memberBased and groups
        if ($this->ignoreCon4GisRights) {
            return false;
        }
        if ($this->brickKey && C4GVersionProvider::isInstalled('con4gis/groups') &&
            (C4GBrickView::isMemberBased($this->viewType) ||
             C4GBrickView::isGroupBased($this->viewType))) {
            if (!MemberModel::hasRightInAnyGroup($user->id, $this->brickKey)) {
                $this->loadLanguageFiles();
                $return = [
                    'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_PERMISSION_DENIED'],
                    'title' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_PERMISSION_DENIED_TITLE'],
                ];

                return json_encode($return);
            }
        }

        return false;
    }

    private function memberCheck($init = false)
    {
        $user = FrontendUser::getInstance();
        if ($user->id > 0) {
            return false;
        } elseif (
            !C4GBrickView::isPublicBased($this->viewType) &&
            !C4GBrickView::isPublicParentBased($this->viewType) &&
            !C4GBrickView::isPublicUUIDBased($this->viewType) &&
            C4GVersionProvider::isInstalled('con4gis/groups')
        ) {
            $this->loadLanguageFiles();

            if ($init) {
                $this->initBrickModule(-1);
                if ($this->withPermissionCheck) {
                    $this->initPermissions();
                }
            }

            if ($this->dialogParams && $this->dialogParams->getViewParams()->getLoginRedirect()) {
                if ($this->dialogParams->getViewParams()->getLoginRedirect() && (($jumpTo = \Contao\PageModel::findByPk($this->dialogParams->getViewParams()->getLoginRedirect())) !== null)) {
                    $return['jump_to_url'] = $jumpTo->getFrontendUrl();

                    return json_encode($return);
                }
            }

            $return = [
                'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_NOT_LOGGED_IN'],
                'title' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_NOT_LOGGED_IN_TITLE'],
            ];

            return json_encode($return);
        } elseif (($this->publicViewType) && (
                ($this->viewType == C4GBrickViewType::PUBLICBASED) ||
                ($this->viewType == C4GBrickViewType::PUBLICPARENTBASED)
            )) {
            $this->viewType = $this->publicViewType;
        }

        return false;
    }

    /**
     * loading all language files
     */
    protected function loadLanguageFiles()
    {
        if ($this->language === '') {
            $language = $GLOBALS['TL_LANGUAGE'];
        } else {
            $language = $this->language;
            $GLOBALS['TL_LANGUAGE'] = $language;
        }

        System::loadLanguageFile('fe_c4g_list', $language);
        System::loadLanguageFile('fe_c4g_dialog', $language);

        if ($this->languageFile) {
            System::loadLanguageFile($this->languageFile, $language);
        }
    }

    public function beforeAction($action)
    {
        //use it in your module class
    }

    public function initBrickModule($id)
    {
        $arrHeadline = property_exists($this, 'headline') ? StringUtil::deserialize($this->headline) : '';
        $this->headline = is_array($arrHeadline) && key_exists('value', $arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
        $hl = property_exists($this,'headlineTag') ? $this->headlineTag : 'h1';
        $this->hl = is_array($arrHeadline) && key_exists('unit', $arrHeadline) ? $arrHeadline['unit'] : $hl;

        //loading language files
        $this->loadLanguageFiles();

        $authenticated = false;
        if (FrontendUser::getInstance()->isLoggedIn) {
            $user = FrontendUser::getInstance();
            $authenticated = $user->authenticate();
        }

        if (!$authenticated && $this->publicViewType && (($this->viewType == C4GBrickViewType::PUBLICBASED) || ($this->viewType == C4GBrickViewType::PUBLICPARENTBASED))) {
            $this->viewType = $this->publicViewType;
        }

        //setting database
        if (!$this->brickDatabase) {
            $databaseParams = new C4GBrickDatabaseParams($this->databaseType);
            $databaseParams->setPkField('id');
            $databaseParams->setTableName($this->tableName);

            if (class_exists($this->entityClass)) {
                $class = new \ReflectionClass($this->entityClass);
                $namespace = $class->getNamespaceName();
                $dbClass = str_replace($namespace . '\\', '', $this->entityClass);
                $dbClass = str_replace('\\', '', $dbClass);
            } else {
                $class = new \ReflectionClass(get_called_class());
                $namespace = str_replace('contao\\modules', 'database', $class->getNamespaceName());
                $dbClass = $this->modelClass;
            }

            $databaseParams->setFindBy($this->findBy);
            $databaseParams->setEntityNamespace($namespace);

            //ToDo
            $database = Database::getInstance();
            $databaseParams->setDatabase($database);

            if ($this->databaseType == C4GBrickDatabaseType::DCA_MODEL) {
                $databaseParams->setModelClass($this->modelClass);
            } else {
                if ($this->modelClass) {
                    $databaseParams->setModelClass($this->modelClass);
                }
                $databaseParams->setEntityClass($dbClass);
            }

            $this->brickDatabase = new C4GBrickDatabase($databaseParams);
        }

        $user = FrontendUser::getInstance();

        //setting list params
        //ToDo ViewType berücksichten (bei Formularen nicht notwendig)
        if (!$this->listParams) {
            $this->listParams = new C4GBrickListParams($this->brickKey, $this->viewType, $this->session);
            $this->listParams->setWithModelListFunction(!empty($this->modelListFunction));
            //$this->listParams->setWithModelDialogFunction(!empty($this->modelDialogFunction));

            $groups = C4GBrickCommon::getGroupListForBrick($user->id, $this->brickKey);
            $groupCount = count($groups);
            $this->listParams->setGroupCount($groupCount);
            $this->listParams->setWithJQueryUI($this->jQueryAddJqueryUI);
            $this->listParams->setCaptionField($this->captionField);
        }

        if (!$this->settings) {
            $settings = Database::getInstance()->execute('SELECT * FROM tl_c4g_settings LIMIT 1')->fetchAllAssoc();

            if ($settings) {
                $this->settings = $settings[0];
            }
        }

        //setting dialog params
        if (!$this->dialogParams) {
            $this->dialogParams = new C4GBrickDialogParams($this->brickKey, $this->viewType, $this->session);
            $this->dialogParams->setGroupId($this->group_id);
            $this->dialogParams->setProjectId($this->project_id);
            $this->dialogParams->setProjectUuid($this->project_uuid);
            $this->dialogParams->setParentId($this->parent_id);
            $this->dialogParams->setMemberId($user->id);
            $this->dialogParams->setProjectKey($this->projectKey);
            $this->dialogParams->setParentModel($this->parentModel);
            $this->dialogParams->setParentCaption($this->parentCaption);
            $this->dialogParams->setParentCaptionPlural($this->parentCaptionPlural);
            if ($this->brickCaption) {
                $this->dialogParams->setBrickCaption($this->brickCaption);
            }
            if ($this->brickCaptionPlural) {
                $this->dialogParams->setBrickCaptionPlural($this->brickCaptionPlural);
            }
            $this->dialogParams->setC4gMap($this->c4g_map);
            $contentId = property_exists($this,'contentId') ? $this->contentid : 0;
            if (!$contentId) {
                $contentId = isset($this->settings['position_map']) ? $this->settings['position_map'] : null;
            }
            $this->dialogParams->setContentId($contentId);

            $this->dialogParams->setHeadline($this->headline);
            $this->dialogParams->setHeadlineTag($this->hl ?: $this->headlineTag);

            $this->dialogParams->setSendEMails($this->sendEMails);
            $this->dialogParams->setWithNotification($this->withNotification);

            if (property_exists($this,'notification_type')) {
                $this->dialogParams->setNotificationType($this->notification_type);
            }
            if (property_exists($this, 'notification_type_contact_request')) {
                $this->dialogParams->setNotificationTypeContactRequest($this->notification_type_contact_request);
            }
            $this->dialogParams->setWithActivationInfo($this->withActivationInfo);
            $this->dialogParams->setPopup($this->isPopup);
            $this->dialogParams->setWithBackup($this->withBackup);
            $this->dialogParams->setRedirectBackSite(property_exists($this,'redirect_back_site') ? $this->redirect_back_site : '');
            $this->dialogParams->setParentIdField($this->parentIdField);
            $this->dialogParams->setRedirectSite(property_exists($this,'redirect_site') ? $this->redirect_site : '');
            $this->dialogParams->setCaptionField($this->captionField);
        }

        //setting view params
        if (!$this->viewParams) {
            $this->viewParams = new C4GBrickViewParams($this->viewType);
            $this->viewParams->setModelListFunction($this->modelListFunction);
            $this->viewParams->setModelDialogFunction($this->modelDialogFunction);
            $this->dialogParams->setViewParams($this->viewParams);
        }

        //setting id on every call


        $this->session->setSessionValue('c4g_brick_dialog_id', $id);

        if ($this->permalink_field !== 'id') {
            $alias = '';
            $permalinkField = $this->permalink_field;
            if ($id && $id != -1 && $this->modelClass) {
                $model = $this->modelClass;
                $object = $model::findByPk($id);
                if ($object) {
                    $alias = $object->$permalinkField;
                    $this->session->setSessionValue('c4g_brick_dialog_' . $permalinkField, $alias);
                }
            }
        }

        if ($id) {
            $this->dialogParams->setId($id);
        }

        //Setting UUID
        if ($this->useUuidCookie) {
            if (!($_COOKIE[$this->UUID])) {
                if (!$this->session->getSessionValue->get($this->UUID)) {
                    $this->session->setSessionValue($this->UUID, C4GBrickCommon::getGUID());
                }
                setcookie($this->UUID, $this->session->getSessionValue($this->UUID), time() + 60 * 60 * 24 * 30, '/');
            } else {
                $this->session->setSessionValue($this->UUID, $_COOKIE[$this->UUID]);
                setcookie($this->UUID, ($_COOKIE[$this->UUID]), time() + 60 * 60 * 24 * 30, '/');
            }
            if ($this->session->getSessionValue($this->UUID)) {
                $this->dialogParams->setUuid($this->session->getSessionValue($this->UUID));
                $this->listParams->setUuid($this->session->getSessionValue($this->UUID));
            }

            //Synchronize MemberBased and PublicUuidBased view types
            if ((C4GBrickView::isMemberBased($this->viewType)) || (C4GBrickView::isPublicUUIDBased($this->viewType))) {
                if (($this->dialogParams->getMemberID() > 0) && ($this->dialogParams->getUuid())) {
                    $database = \Database::getInstance();
                    //in case the module table does not have a member_id field (otherwise an exception will be thrown and the site won't work)
                    $query = $database->prepare("SHOW COLUMNS FROM $this->tableName LIKE 'member_id'")->execute();
                    if ($query->numRows) {
                        $query = $database->prepare('SELECT * FROM ' . $this->tableName .
                            " WHERE member_id = 0 AND uuid = '" . $this->dialogParams->getUuid() . "'")->execute();
                        if ($query) {
                            $stmt = $database->prepare('UPDATE ' . $this->tableName . ' SET member_id = ' . $this->dialogParams->getMemberID()
                                . " WHERE member_id = 0 AND uuid = '" . $this->dialogParams->getUuid() . "'");
                            $stmt->execute();
                            if (($this->dialogParams->getMemberID() !== 0) && ($this->dialogParams->getMemberID() !== '0')) {
                                $stmt = $database->prepare('UPDATE ' . $this->tableName . " SET uuid = '" . $this->dialogParams->getUuid()
                                    . "' WHERE member_id = '" . $this->dialogParams->getMemberID() . "'");
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
        }

        //set fieldList
        if (!$this->fieldList) {
            $putVars = $this->session->getSessionValue('c4g_brick_dialog_values');
            $result = $this->addFields();
            if ($result) {
                $this->fieldList = $result;
                $this->dialogChangeHandler = new C4GDialogChangeHandler($this->session);
                $this->fieldList = $this->dialogChangeHandler->reapplyChanges($this->brickKey, $this->fieldList);
            }
            if ($this->fieldList && $putVars && $this->renewInitialValues()) {
                $this->setInitialValues($putVars);
            }
        }
    }

    /**
     * @param $putVars
     */
    private function setInitialValues($putVars)
    {
        if ($this->fieldList && $putVars) {
            foreach ($this->fieldList as $field) {
                $value = $putVars[$field->getFieldName()];
                if ($value) {
                    $field->setInitialValue($value);
                }
            }
        }
    }

    protected function compileJquery()
    {
        C4GJQueryGUI::initializeBrickLibraries(
            $this->jQueryAddCore,
            $this->jQueryAddJquery,
            $this->jQueryAddJqueryUI,
            $this->jQueryUseTree,
            $this->jQueryUseTable,
            $this->jQueryUseHistory,
            $this->jQueryUseTooltip,
            $this->jQueryUseMaps,
            $this->jQueryUseScrollPane,
            $this->isPopup,
            $this->loadDateTimePickerResources
        );

        //ToDo remove jQueryUI
        if ($this->jQueryAddJqueryUI || $this->jQueryUseTable || $this->jQueryUseTree) {
            $settings = Database::getInstance()->execute('SELECT * FROM tl_c4g_settings LIMIT 1')->fetchAllAssoc();

            if ($settings) {
                $this->settings = $settings[0];
            }

            // load custom themeroller-css if set
            if ($this->uiTheme) {
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $this->uiTheme;
            } elseif (property_exists($this, 'c4g_appearance_themeroller_css') && $this->c4g_appearance_themeroller_css) {
                $objFile = \FilesModel::findByUuid($this->c4g_appearance_themeroller_css);
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
            } elseif (property_exists($this, 'c4g_uitheme_css_select') && $this->c4g_uitheme_css_select) {
                $theme = $this->c4g_uitheme_css_select;
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/ui-themes/themes/' . $theme . '/jquery-ui.css';
            } elseif ($this->settings && $this->settings['c4g_appearance_themeroller_css']) {
                $objFile = \FilesModel::findByUuid($this->settings['c4g_appearance_themeroller_css']);
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
            } elseif ($this->settings && $this->settings['c4g_uitheme_css_select']) {
                $theme = $this->settings['c4g_uitheme_css_select'];
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/ui-themes/themes/' . $theme . '/jquery-ui.css';
            } else {
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/ui-themes/themes/base/jquery-ui.css';
            }
        }
    }

    protected function compileJavaScript()
    {
        if ($this->loadDefaultResources) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/C4GBrickDialog.js', ResourceLoader::BODY, 'c4g_brick_dialog');
        }
        if ($this->loadConditionalFieldDisplayResources) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/ConditionalFieldDisplay.js', ResourceLoader::BODY);
        }
        if ($this->loadMoreButtonResources) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/more-button.js', ResourceLoader::BODY);
        }
        if ($this->brickScript) {
            ResourceLoader::loadJavaScriptResource($this->brickScript, ResourceLoader::BODY, 'c4g_brick_script_' . $this->name);
        }
        if ($this->loadTriggerSearchFromOtherModuleResources) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/datatable-search-trigger.js', ResourceLoader::BODY, 'datatable-search-trigger');
        }
        if ($this->loadChosenResources) {
            ResourceLoader::loadJavaScriptResource('bundles/con4giscore/vendor/jQuery/plugins/chosen/chosen.jquery.min.js', ResourceLoader::BODY, 'chosen-jquery');
            ResourceLoader::loadJavaScriptResourceTag('ready(function () {jQuery(".chzn-select").chosen();})');
        }
        if ($this->loadFileUploadResources) {
            ResourceLoader::loadJavaScriptResourceTag('var uploadApiUrl = \'con4gis/api/fileUpload/\';');
        }

        if ($this->loadTrixEditorResources) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-trix.js', ResourceLoader::BODY, 'trix-editor');
            $get = [];
            global $objPage;
            $get[] = 'lang=' . $objPage->language;
            if (!empty($this->trixEditorResourceParams)) {
                foreach ($this->trixEditorResourceParams as $param) {
                    $get[] = "$param=1";
                }
            }

            $configUrl = 'bundles/con4gisprojects/dist/js/trixconfig.php?' . implode('&', $get);
            ResourceLoader::loadJavaScriptResource($configUrl);
        }
        if ($this->loadMultiColumnResources === true) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/multicolumn.js', ResourceLoader::BODY, 'multicolumn');
        }
        if ($this->loadMiniSearchResources === true) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-minisearch.js', ResourceLoader::HEAD);
        }

        if ($this->loadHistoryPushResources === true) {
            if ($this->loadHistoryBrickPushResources === true) {
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/historyBrickPush.js', ResourceLoader::BODY, 'history-push');
            } else {
                ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/historyPush.js', ResourceLoader::BODY, 'history-push');
            }
        }

        if ($this->loadSignaturePadResources === true) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/vendor/signature-pad/flashcanvas.js', ResourceLoader::BODY, 'flashcanvas');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/vendor/signature-pad/jquery.signaturepad.min.js', ResourceLoader::BODY, 'signature-pad');
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/vendor/signature-pad/json2.min.js', ResourceLoader::BODY, 'json2');
        }

        if ($this->loadDatePicker === true) {
            ResourceLoader::loadJavaScriptResource('bundles/con4gisprojects/dist/js/c4g-vendor-datepicker.js', ResourceLoader::BODY, 'vanillajs-datepicker');
        }
    }

    protected function compileCss()
    {
        if ($this->loadFontAwesomeResources) {
            ResourceLoader::loadCssResource('bundles/con4giscore/dist/css/fontawesome.min.css', 'fontawesome');
        }
        if ($this->loadDefaultResources) {
            ResourceLoader::loadCssResource('bundles/con4gisprojects/dist/css/c4g-brick.min.css',
                'c4g_brick_style');
        }
        if ($this->brickStyle) {
            ResourceLoader::loadCssResource($this->brickStyle, 'c4g_brick_style_' . $this->name);
        }
        if ($this->loadDateTimePickerResources) {
            ResourceLoader::loadCssResource(
                'bundles/con4giscore/vendor/jQuery/plugins/' .
                'jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.css',
                'simple-dtpicker');
        }
        if ($this->loadChosenResources) {
            ResourceLoader::loadCssResource('bundles/con4giscore/vendor/jQuery/plugins/chosen/chosen.css');
        }

        if ($this->loadTrixEditorResources) {
            ResourceLoader::loadCssResource('bundles/con4gisprojects/dist/css/trix.min.css');
        }

        if ($this->loadSignaturePadResources === true) {
            ResourceLoader::loadCssResource('bundles/con4gisprojects/vendor/signature-pad/jquery.signaturepad.css');
        }
    }

    /**
     * @param null $request
     * @return false|string
     * @throws \Exception
     */
    public function generateAjax($request = null)
    {
        // auf die benutzerdefinierte Fehlerbehandlung umstellen
        // $old_error_handler = set_error_handler("c4gGroupsErrorHandler");
        if ($request == null) {

            // Ajax Request: read get parameter "req"
            $request = $_GET['req'];
            if ($request != 'undefined') {
                // replace "state" parameter in Session-Referer to force correct
                // handling after login with "redirect back" set
                $session = $this->session->getSession()->all();
                $session['referer']['last'] = $session['referer']['current'];
                // echo '<br>[LAST]' . $session['referer']['last'] . '<br>';
                $session['referer']['current'] = C4GUtils::addParametersToURL(
                    $session['referer']['last'],
                    ['state' => $request]);
                // echo '<br>[CURRENT]' . $session['referer']['current'] . '<br>';
                $this->session->getSession()->replace($session);
            }
        }

        //ToDo prüfen ob der Try/Catch Block hier immer noch hilfreich ist.
        try {
            $user = FrontendUser::getInstance();

            $result = $this->memberCheck(true);
            if ($result) {
                return $result;
            }

            $result = $this->groupCheck();
            if ($result) {
                return $result;
            }

            $session = $this->session->getSession()->all();

            if (C4GBrickView::isGroupBased($this->viewType)) {
                if (($this->group_id == -1) || ($this->group_id == null)) {
                    $groupId = $this->session->getSessionValue('c4g_brick_group_id');
                    if ($groupId && MemberGroupModel::isMemberOfGroup($groupId, $user->id)) {
                        if (MemberModel::hasRightInGroup($user->id, $groupId, $this->brickKey)) {
                            $this->group_id = $groupId;
                        }
                    }

                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_GROUP_DATA);

                    $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id;
                    C4GBrickCommon::mkdir($path);
                }
            } elseif (C4GBrickView::isProjectBased($this->viewType)) {
                if (($this->group_id == -1) || ($this->group_id == null)) {
                    $groupId = $this->session->getSessionValue('c4g_brick_group_id');
                    if (C4GBrickAction::checkGroupId($groupId, $user->id, $this->brickKey)) {
                        $this->group_id = $groupId;
                    }

                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_GROUP_DATA);

                    $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id;
                    C4GBrickCommon::mkdir($path);
                }

                if (($this->projectCount == -1) || ($this->projectCount == null)) {
                    $this->projectCount = count(C4gProjectsModel::getProjectListForBrick($user->id, $this->group_id, $this->projectKey));
                }

                if (($this->project_id == -1) || ($this->project_id == null)) {
                    $project_id = $this->session->getSessionValue('c4g_brick_project_id');
                    if (C4gProjectsModel::checkProjectId($project_id, $this->projectKey, $this->session)) {
                        $this->project_id = $project_id;
                        $this->project_uuid = $this->session->getSessionValue('c4g_brick_project_uuid');
                        $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id . '/' . $this->project_uuid;
                        C4GBrickCommon::mkdir($path);
                    }
                }
            } elseif (C4GBrickView::isProjectParentBased($this->viewType)) {
                if (($this->group_id == -1) || ($this->group_id == null)) {
                    $groupId = $this->session->getSessionValue('c4g_brick_group_id');
                    if (C4GBrickAction::checkGroupId($groupId, $user->id, $this->brickKey)) {
                        $this->group_id = $groupId;
                    }

                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_GROUP_DATA);

                    $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id;
                    C4GBrickCommon::mkdir($path);
                }

                if (($this->projectCount == -1) || ($this->projectCount == null)) {
                    $this->projectCount = count(C4gProjectsModel::getProjectListForBrick($user->id, $this->group_id, $this->projectKey));
                }

                if (($this->project_id == -1) || ($this->project_id == null)) {
                    $project_id = $this->session->getSessionValue('c4g_brick_project_id');
                    if (C4gProjectsModel::checkProjectId($project_id, $this->projectKey, $this->session)) {
                        $this->project_id = $project_id;
                        $this->project_uuid = $this->session->getSessionValue('c4g_brick_project_uuid');
                        $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id . '/' . $this->project_uuid;
                        C4GBrickCommon::mkdir($path);
                    }
                }

                if (($this->parent_id == -1) || ($this->parent_id == null)) {
                    $this->parent_id = $this->session->getSessionValue('c4g_brick_parent_id');
                }
            } elseif (C4GBrickView::isMemberBased($this->viewType)) {
                C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                C4GBrickCommon::mkdir(C4GBrickConst::PATH_MEMBER_DATA);

                $path = C4GBrickConst::PATH_MEMBER_DATA . '/' . $user->id;
                C4GBrickCommon::mkdir($path);

            //Hier wird nur die UserId benötigt und die steht überall zur Verfügung.
            } else {
                if (($this->group_id == -1) || ($this->group_id == null)) {

                    //falls die group_id über die Liste vorhanden ist, soll diese auch hier für Module geladen werden, die den viewType wechseln können.
                    $groupId = $this->session->getSessionValue('c4g_brick_group_id');
                    if ($groupId && MemberGroupModel::isMemberOfGroup($groupId, $user->id)) {
                        if (MemberModel::hasRightInGroup($user->id, $groupId, $this->brickKey)) {
                            $this->group_id = $groupId;
                        }
                    }
                }
            }

            $currentReferer = $session && key_exists('referer', $session) && is_array($session['referer']) && key_exists('current',$session['referer']) ? $session['referer']['current'] : '';

            // $path = $this->getParameter('kernel.project_dir');
            $path = System::getContainer()->getParameter('kernel.project_dir');
            $this->frontendUrl = Environment::get('url') . $path . '/' . $currentReferer;

            if (key_exists('REQUEST_METHOD', $_SERVER) && ($_SERVER['REQUEST_METHOD'] == 'PUT')) {
                parse_str(file_get_contents('php://input'), $this->putVars);
                foreach ($this->putVars as $key => $putVar) {
                    $tmpVar = C4GUtils::secure_ugc($putVar);
                    $tmpVar = C4GUtils::cleanHtml($tmpVar);
                    $this->putVars[$key] = $tmpVar;
                }
            }

            // if there was an initial get parameter "state" then use it for jumping directly
            // to the refering function
            if (($request == 'initnav') && (key_exists('initreq',$_GET) && $_GET['initreq'])) {
                $_GET['historyreq'] = $_GET['initreq'];
            }

            if ($this->permalink_name && key_exists($this->permalink_name, $_GET) && is_numeric($_GET[$this->permalink_name])) {
                $permalinkField = 'id'; //ToDo other solution to work parallel with id and alias
            } else {
                $permalinkField = $this->permalink_field;
            }

            //permalink (1 permalink=id / 2 fieldname=id / 3 permalink_name=id)
            if ($permalinkField &&
                    (
                        (key_exists(C4GBrickActionType::IDENTIFIER_PERMALINK,$_GET) && $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]) ||
                        (key_exists($permalinkField,$_GET) && $_GET[$permalinkField]) ||
                        ($this->permalink_name && (key_exists($this->permalink_name,$_GET) && $_GET[$this->permalink_name]))
                    )
            ) {
//                if (!$this->brickDatabase) {
//                    $this->initBrickModule(-1);
//                    if ($this->withPermissionCheck) {
//                        $this->initPermissions();
//                    }
//                }

                if (key_exists(C4GBrickActionType::IDENTIFIER_PERMALINK, $_GET) && $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]) {
                    if (!$this->permalinkModelClass) {
                        $model  = $this->modelClass;
                        $dataset = $model::findBy($permalinkField, $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]);
                    } else {
                        $model = $this->permalinkModelClass;
                        $dataset = $model::findBy($permalinkField, $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]);
                    }

                    if ($dataset) {
                        $id = $dataset->id;
                        $this->initBrickModule($id);
                        if ($this->withPermissionCheck) {
                            $this->initPermissions();
                        }
                        $action = C4GBrickActionType::IDENTIFIER_LIST . ':' . $id;
                        $result = $this->getPerformAction($request, $action);
                    }
                } elseif (key_exists($permalinkField, $_GET) && $_GET[$permalinkField]) {
                    if (!$this->permalinkModelClass) {
                        $model  = $this->modelClass;
                        $dataset = $model::findBy($permalinkField, $_GET[$permalinkField]);
                    } else {
                        $model = $this->permalinkModelClass;
                        $dataset = $model::findBy($permalinkField, $_GET[$permalinkField]);
                    }

                    if ($dataset) {
                        $id = $dataset->id;
                        $this->initBrickModule($id);
                        if ($this->withPermissionCheck) {
                            $this->initPermissions();
                        }
                        $action = C4GBrickActionType::IDENTIFIER_LIST . ':' . $id;
                        $result = $this->getPerformAction($request, $action);
                    }
                } elseif ($this->permalink_name && key_exists($this->permalink_name, $_GET) && $_GET[$this->permalink_name]) {
                    if (!$this->permalinkModelClass) {
                        $model  = $this->modelClass;
                    } else {
                        $model = $this->permalinkModelClass;
                    }
                    $dataset = $model::findBy($permalinkField, $_GET[$this->permalink_name]);
                    if ($dataset) {
                        $id = $dataset->id;
                        $this->initBrickModule($id);
                        if ($this->withPermissionCheck) {
                            $this->initPermissions();
                        }
                        $action = C4GBrickActionType::IDENTIFIER_LIST . ':' . $id;
                        $result = $this->getPerformAction($request, $action);
                    }
                }
                // History navigation
            } elseif (key_exists('historyreq', $_GET) && $_GET['historyreq']) {
                $actions = explode(';', $_GET['historyreq']);
                $result = [];
                foreach ($actions as $action) {
                    $r = $this->performHistoryAction($action);
                    ArrayUtil::arrayInsert($result, 0, $r);
                }
            } else {
                switch ($request) {
                    case 'initnav':
                        $action = C4GBrickActionType::IDENTIFIER_LIST . ':-1';
                        $result = $this->getPerformAction($request, $action);

                        break;
                    default:
                        $actions = explode(';', $request);
                        if (strpos($actions[0], ':') === false) {
                            //Formulardialog
                            $pos = strpos($actions[0], C4GBrickActionType::IDENTIFIER_DIALOG);
                            if (($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_DIALOG));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_DIALOG . ':' . $id;
                            }
                            $pos = strpos($actions[0], C4GBrickActionType::IDENTIFIER_BRICKDIALOG);
                            if (($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_BRICKDIALOG));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_BRICKDIALOG . ':' . $id;
                            }

                            //Messagedialog
                            $pos = strpos($actions[0], C4GBrickActionType::IDENTIFIER_MESSAGE);
                            if (($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_MESSAGE));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_MESSAGE . ':' . $id;
                            }

                            //Selectdialog
                            $pos = strpos($actions[0], C4GBrickActionType::IDENTIFIER_SELECT);
                            if (($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_SELECT));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_SELECT . ':' . $id;
                            }
                        }

                        /*if ( ($actions[0] == '-1') ) {
                          $actions[0] = C4GBrickActionType::IDENTIFIER_LIST; //tritt evtl. auf, wenn das Browserfenster neu geladen wird, so greiftn default in performAction
                        }*/

                        $result = [];
                        foreach ($actions as $action) {
                            $r = $this->getPerformAction($request, $action);
                            if (is_array($r)) {
                                $result = array_merge($result, $r);
                            } else {
                                $r = json_decode($r, true);
                                if (is_array($r)) {
                                    $result = array_merge($result, $r);
                                }
                            }
                        }
                }
            }
        } catch (Exception $e) {
            $result = $this->showException($e);
        }

        if ($this->permalink_name && $permalinkField && $permalinkField !== 'id' && $this->session->getSessionValue("c4g_brick_dialog_".$permalinkField) && ($result['dialogstate'] == "item:")) {
            $result['dialogstate'] = str_replace('item:', $this->permalink_name.'='. $this->session->getSessionValue("c4g_brick_dialog_".$permalinkField), $result['dialogstate']);
        } else if ($this->permalink_name && $this->session->getSessionValue("c4g_brick_dialog_id") && ($result['dialogstate'] == "item:")) {
            $result['dialogstate'] = str_replace('item:', $this->permalink_name.'='. $this->session->getSessionValue("c4g_brick_dialog_id"), $result['dialogstate']);
        } else if ($this->permalink_name && $result && key_exists('dialogstate', $result)) {
            $result['dialogstate'] = str_replace('item:', $this->permalink_name.'=', $result['dialogstate']);
        }

        return json_encode($result);
    }

    /**
     * @Route(
     *      path="/projects-api/perform/{action}",
     *      methods={"GET"}
     *  )
     * module event controller
     *
     * @param $action
     * @return array|mixed
     */
    #[Route(
        path: '/projects-api/perform/{action}',
        methods: ['GET']
    )]
    public function getPerformAction($request, $action, $withMemberCheck = true)
    {
        $values = explode(':', $action, 5);
        if (is_numeric($values[1])) {
            $this->initBrickModule($values[1]);
        } elseif ($values[0] == C4GBrickActionType::ACTION_BUTTONCLICK && is_numeric($values[2])) {
            // this case is needed for the ACTION_BUTTONCLICK action
            $this->initBrickModule($values[2]);
        } else {
            $this->initBrickModule($values[1]);
        }
        if ($this->withPermissionCheck) {
            $this->initPermissions();
        }

        if ($withMemberCheck) {
            $result = $this->memberCheck();
            if ($result) {
                return $result;
            }

            $result = $this->groupCheck();
            if ($result) {
                return $result;
            }
        }

        //special event because of calling module function
        if ($values[0] == C4GBrickActionType::ACTION_BUTTONCLICK) {
            $function = strval($values[1]);

            $putVars = $this->putVars;
            if (!$putVars || (count($putVars) <= 0)) {
                $putVars = $this->session->getSessionValue('c4g_brick_dialog_values');
            }

            foreach ($this->fieldList as $field) {
                $fieldName = $field->getFieldName();
                if ($putVars && $fieldName && key_exists($fieldName, $putVars)) {
                    $putVars[$fieldName] = $field->validateFieldValue($putVars[$fieldName]);
                }
            }

            //id lost with button field (ONCLICK_TYPE_SERVER)
            if ($putVars && $values[2]) {
                $putVars['id'] = $values[2];
            }

            $result = $this->$function($values, $putVars);

            return $result;
        }
        $this->beforeAction($action);

        return C4GBrickAction::performAction($action, $this);
    }

    /**
     * Initialize C4GPermissions for this module.
     */
    private function initPermissions()
    {
        if (C4GBrickView::isWithoutEditing($this->viewType)) {
            $level = 1;
        } else {
            $level = 2;
        }
        $permission = $this->getC4GTablePermission($this->viewType);
        if ($permission instanceof C4GTablePermission) {
            $permission->setLevel($level);
            $permission->set();
        } elseif (is_array($permission)) {
            foreach ($permission as $perm) {
                if ($perm instanceof C4GTablePermission) {
                    $permission->setLevel($level);
                    $permission->set();
                }
            }
        }
    }

    /**
     * Get the permissions given by this module.
     * Modules that need non-standard permissions MUST override this method.
     * This is common if your module uses a model function to load database values.
     * The return value must be an instance of C4GTablePermission or an array of instances of C4GTablePermission.
     * @param string $viewType This module's viewtype.
     * @return C4GTablePermission
     */
    public function getC4GTablePermission($viewType)
    {
        //Untested view types: (May or may not work, also may or may not throw exceptions)
        //Todo MemberForm
        //Todo GroupProject
        //Todo GroupParentView
        //Todo GroupParentBased
        //Todo GroupView
        //Todo GroupForm
        //Todo GroupFormCopy

        //Might need special attention: (because they might fall into multiple cases)
        //Todo GroupParentView is part of isGroupBased and isGroupParentBased

        //If you need to handle a view type specifically, use "case $viewType == C4GBrickViewType::VIEW_TYPE:"
        //but try to keep the number of cases civil.

        //Also keep in mind you might have to find a non-standard (i.e. module specific) solution.

        $elements = null;
        switch (true) {
            case $viewType == C4GBrickViewType::GROUPPARENTBASED:
                $id = $this->getDialogParams()->getGroupId();
                $idField = $this->viewParams->getGroupKeyField();

                break;
            case $viewType == C4GBrickViewType::ADMINBASED:
                $idField = '';

                break;
            case C4GBrickView::isMemberBased($viewType):
                $id = $this->getDialogParams()->getMemberId();
                $idField = $this->viewParams->getMemberKeyField();

                break;
            case C4GBrickView::isWithParent($viewType):
                $id = $this->getDialogParams()->getParentId();
                $idField = $this->viewParams->getParentKeyField();

                break;
            case C4GBrickView::isPublicUUIDBased($viewType):
                $id = $this->getDialogParams()->getUuid();
                $idField = 'uuid';

                break;
            case C4GBrickView::isGroupBased($viewType):
                $id = $this->getDialogParams()->getGroupId();
                $idField = $this->viewParams->getGroupKeyField();

                break;
            case C4GBrickView::isProjectBased($viewType):
                $id = $this->getDialogParams()->getProjectId();
                $idField = 'project_id';

                break;
            default:
                $id = 0;
                $idField = '';

                break;
        }

        if (($idField !== '') && ($id) && $this->databaseType !== C4GBrickDatabaseType::NO_DB) {
            $elements = $this->brickDatabase->findBy($idField, $id);
        } elseif ($viewType == C4GBrickViewType::ADMINBASED) {
            $elements = $this->brickDatabase->findAll();
        } else {
            $elements = null;
        }
        $array = [];
        if ($elements != null) {
            foreach ($elements as $element) {
                if ($element instanceof \stdClass) {
                    $array[] = $element->id;
                } else {
                    $e = $element->row();
                    $array[] = $e['id'];
                }
            }
        }
        if (is_array($array) && (sizeof($array) > 0)) {
            $result = new C4GTablePermission($this->getC4GTablePermissionTable(), $array, $this->session);

            return $result;
        }

        return null;
    }

    /**
     * Get the table this module needs permission for. Most of the time, this is $this->tableName.
     * If for whatever reason the module needs access to a different table, override this method.
     * @return string
     */
    public function getC4GTablePermissionTable()
    {
        return $this->tableName;
    }

    /**
     * @param $historyAction
     * @return array|mixed
     */
    public function performHistoryAction($historyAction)
    {
        $values = explode(':', $historyAction);
        $this->action = $values[0];

        $request = null; //ToDO
        $result = $this->getPerformAction($request, $historyAction);

        // close all dialogs that have been open to avoid conflicts
        $result['dialogcloseall'] = true;

        return $result;
    }

    /**
     * @param $newId
     * @param $notifyOnChanges
     * @param $notification_type
     * @param $dlgValues
     * @param $fieldList
     */
    public function sendNotifications($newId, $notifyOnChanges, $notification_type, $dlgValues, $fieldList, $changes)
    {
        if ($newId || $notifyOnChanges) {
            $notification_array = StringUtil::deserialize($notification_type);

            if ($notification_array && is_array($notification_array)) {
                if (is_array($notification_array) && sizeof($notification_array) == 1) {
                    // $objNotification = NotificationCenter::findByPk($notification_array);
                    // if ($objNotification !== null) {
                        $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList);
                        $arrTokens['admin_email'] = $GLOBALS['TL_CONFIG']['adminEmail'];
                        // $objNotification->send($arrTokens);
                        System::getContainer()->get('con4gis\ReservationBundle\Classes\Notifications\C4gNotificationCenterService')->getNotificationCenter()->sendNotification(intval($notification_array[0]), $arrTokens);
                    // }
                } else {
                    foreach ($notification_array as $key=>$notification) {
                        // $objNotification = NotificationCenter::findByPk($notification);
                        // if ($objNotification !== null) {
                            $arrTokens = C4GBrickNotification::getArrayTokens($dlgValues, $fieldList);
                            $arrTokens['admin_email'] = $GLOBALS['TL_CONFIG']['adminEmail'];
                            System::getContainer()->get('con4gis\ReservationBundle\Classes\Notifications\C4gNotificationCenterService')->getNotificationCenter()->sendNotification($notification_array[$key], $arrTokens);
                            // $objNotification->send($arrTokens);
                        // }
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getBrickCaption()
    {
        return $this->brickCaption;
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
     * @return string
     */
    public function getBrickCaptionPlural()
    {
        return $this->brickCaptionPlural;
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
     * @return null
     */
    public function getBrickDatabase()
    {
        return $this->brickDatabase;
    }

    /**
     * @param $brickDatabase
     * @return $this
     */
    public function setBrickDatabase($brickDatabase)
    {
        $this->brickDatabase = $brickDatabase;

        return $this;
    }

    /**
     * @return null
     */
    public function getListParams()
    {
        return $this->listParams;
    }

    /**
     * @param $listParams
     * @return $this
     */
    public function setListParams($listParams)
    {
        $this->listParams = $listParams;

        return $this;
    }

    /**
     * @return null
     */
    public function getDialogParams()
    {
        return $this->dialogParams;
    }

    /**
     * @param $dialogParams
     * @return $this
     */
    public function setDialogParams($dialogParams)
    {
        $this->dialogParams = $dialogParams;

        return $this;
    }

    /**
     * @return null
     */
    public function getPutVars()
    {
        return $this->putVars;
    }

    /**
     * @param $putVars
     * @return $this
     */
    public function setPutVars($putVars)
    {
        $this->putVars = $putVars;

        return $this;
    }

    /**
     * @return null
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * @param $fieldList
     * @return $this
     */
    public function setFieldList($fieldList)
    {
        $this->fieldList = $fieldList;

        return $this;
    }

    /**
     * logbook function for filling brick select element.
     * @return null
     */
    protected function getBrickSelect()
    {
        return null;
    }

    /**
     * @param $singular
     * @param $plural
     * @return void
     */
    protected function setBrickCaptions($singular, $plural)
    {
        $this->brickCaption = $singular;
        $this->brickCaptionPlural = $plural;

        if ($this->dialogParams) {
            $this->dialogParams->setBrickCaption($singular);
            $this->dialogParams->setBrickCaptionPlural($plural);
        }
    }

    /**
     * @param $singular
     * @param $plural
     * @return void
     */
    protected function setParentCaptions($singular, $plural)
    {
        $this->parentCaption = $singular;
        $this->parentCaptionPlural = $plural;
        if ($this->dialogParams) {
            $this->dialogParams->setParentCaption($singular);
            $this->dialogParams->setParentCaptionPlural($plural);
        }
    }

    /**
     * @return null
     */
    public function getDialogChangeHandler()
    {
        return $this->dialogChangeHandler;
    }

    /**
     * @param $dialogChangeHandler
     * @return $this
     */
    public function setDialogChangeHandler($dialogChangeHandler)
    {
        $this->dialogChangeHandler = $dialogChangeHandler;

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
     * @return bool
     */
    public function isLoadUrlClear()
    {
        return $this->loadUrlClear;
    }

    /**
     * @param bool $loadUrlClear
     * @return $this
     */
    public function setLoadUrlClear($loadUrlClear = true)
    {
        $this->loadUrlClear = $loadUrlClear;

        return $this;
    }

    /**
     * @return string
     */
    public function getFindBy()
    {
        return $this->findBy;
    }

    /**
     * @param $findBy
     * @return $this
     */
    public function setFindBy($findBy)
    {
        $this->findBy = $findBy;

        return $this;
    }

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return bool
     */
    public function isWithPermissionCheck()
    {
        return $this->withPermissionCheck;
    }

    /**
     * @param bool $withPermissionCheck
     */
    public function setWithPermissionCheck($withPermissionCheck)
    {
        $this->withPermissionCheck = $withPermissionCheck;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return C4GBaseController
     */
    public function setLanguage(string $language): C4GBaseController
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrintTemplate(): string
    {
        return $this->printTemplate;
    }

    /**
     * @return string
     */
    public function getPrintStyle(): string
    {
        return $this->printStyle;
    }
}
