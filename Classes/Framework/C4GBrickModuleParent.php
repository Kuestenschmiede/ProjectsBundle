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

namespace con4gis\ProjectsBundle\Classes\Framework;


use con4gis\CoreBundle\Resources\contao\classes\C4GJQueryGUI;
use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\CoreBundle\Resources\contao\classes\ResourceLoader;
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
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickListParams;
use con4gis\ProjectsBundle\Classes\Models\C4gProjectsModel;
use con4gis\ProjectsBundle\Classes\Permission\C4GTablePermission;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickView;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewParams;
use con4gis\ProjectsBundle\Classes\Views\C4GBrickViewType;
use Contao\Database;
use Symfony\Component\Config\Definition\Exception\Exception;

/**
 * Class C4GBrickModuleParent
 *
 * The brick module parent is needed by all con4gis-Projects based fe modules.
 *
 * @package c4g\projects
 */
class C4GBrickModuleParent extends \Module
{
    //mandatory params
    protected $brickKey             = ''; //unique string key for module request and con4gis-Groups rights.
    protected $viewType             = C4GBrickViewType::GROUPBASED; //see C4GBrickViewType
    protected $publicViewType       = ''; //automatic switch from based to view type
    protected $tableName            = ''; //needed by default DatabaseType
    protected $findBy               = array(); //qualify default dataset
    protected $modelClass           = ''; //needed by default DatabaseType
    protected $fieldList            = null; //fieldlist filled by module class with objects inherited by C4GBrickField
    protected $initialFieldList     = null; //initial fieldlist used for history lookups when the fieldlist is changed
    protected $languageFile         = ''; //loading one language file for yout module class

    //optional and modifiable params

    //embedding additional sources
    protected $strTemplate          = 'mod_c4g_brick_list'; //default module template
    protected $brickStyle           = ''; //loading one additional css file for your module class
    protected $brickScript          = ''; //loading one additional js file for your module class
    protected $uiTheme              = ''; //loading your own ui theme for your module class (https://jqueryui.com/themeroller/)

    //group params
    protected $group_id             = -1; //the current group id for group-
    protected $ignoreCon4GisRights = false; //Press true to use Contao rights only

    //project params
    protected $project_id           = -1; //the current project id for projectbased modules.
    protected $project_uuid         = null; //the current project uuid for projectbased im- and export.
    protected $projectKey           = ''; //needed to control project based modules.

    //parent params
    protected $parent_id            = ''; //the current parent id for parentbased modules.
    protected $parentCaption        = ''; //hint caption for parent context messages.
    protected $parentCaptionPlural  = ''; //hint plural caption for parent context messages.
    protected $parentModel          = ''; //needed with ViewType PROJECTPARENTBASED and GROUPPARENTVIEW.
    protected $parentIdField        = ''; //needed if not default pid field.

    //doctrine params
    protected $databaseType         = C4GBrickDatabaseType::DCA_MODEL; //see C4gBrickDatabaseType
    protected $entityClass          = '';

    //con4gis global settings
    protected $settings             = null; //tl_c4g_settings

    //caption params
    protected $brickCaption         = ''; //default singular dataset caption
    protected $brickCaptionPlural   = ''; //default plural dataset caption
    protected $captionField         = 'caption'; //caption field for singular dataset caption needed for logging (logbook)

    //global objects
    protected $brickDatabase        = null; //brick database functions
    protected $listParams           = null; //list based params (needed for list and tiles -> see C4GBrickListParams)
    protected $dialogParams         = null; //dialog based params (needed by dialogs -> see C4GBrickDialogParams)
    protected $dialogChangeHandler  = null;
    protected $putVars              = null; //default dialog values
    protected $viewParams           = null; //view based params (needed for list, tiles and dialogs -> see C4gBrickViewParams)

    //matching params
    protected $searchTable          = null; //searching in tablename -> see C4GMatching

    //email params
    protected $sendEMails           = null; //see -> C4GBrickSendEMail
    protected $withNotification     = false; //using notification center

    //expert params
    protected $modelListFunction    = null; //loading dataset by a special function
    protected $modelDialogFunction  = null; //loading dataset by a special function
    protected $withBackup           = false; //doing automaticly exports (backups)
    protected $withActivationInfo   = false; //activation info
    protected $withLabels           = true; //switching on/off all labels
    protected $isPopup              = false; //needed with magnific popup
    protected $c4g_map              = false; //needed for embedding con4gis maps
    protected $permalink_field      = null; //using another field for permalink (default: id field)
    protected $permalink_name       = null; //for setting an own get param
    protected $permalinkModelClass  = null; //if table filled by modelListFunction
    protected $loadUrlClear         = false; // if true, a js script will be loaded and trim the urls to remove the states
    protected $withPermissionCheck  = true; // can be set to false to avoid the table permission check

    //UUID params
    protected $UUID                 = 'c4g_brick_uuid'; //Name of the uuid cookie in the browser. Can be overridden in child.
    protected $useUuidCookie        = false; //Can be overridden in child to suppress the uuid cookie.

    protected $asnycList            = false; // set true when the list should be loaded after the initial page load


    /**
     * module class function to get fields
     */
    public function addFields()
    {
        //to fill $this->fieldList in your module class
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
     * @param $type
     */
    protected function getIdByType($type) {
        switch($type) {
            case C4GBrickConst::ID_TYPE_MEMBER:
                if (FE_USER_LOGGED_IN) {
                    \System::import('FrontendUser', 'User');
                    $this->User->authenticate();
                }
                return $this->User->id;
                break;
            case C4GBrickConst::ID_TYPE_GROUP:
                $group_id = $this->dialogParams->getGroupId();
                if (!$group_id || ($group_id <= 0)) {
                    $group_id = \Session::getInstance()->get("c4g_brick_group_id");
                }
                return $group_id;
                break;
            case C4GBrickConst::ID_TYPE_PROJECT:
                $project_id = $this->dialogParams->getProjectId();
                if (!$project_id || ($project_id <= 0)) {
                    $project_id = \Session::getInstance()->get("c4g_brick_project_id");
                }
                return $project_id;
                break;
            case C4GBrickConst::ID_TYPE_PARENT:
                $parent_id = $this->dialogParams->getParentId();
                if (!$parent_id || ($parent_id <= 0)) {
                    $parent_id = \Session::getInstance()->get("c4g_brick_parent_id");
                }
                return $parent_id;
                break;
            Default:
                return false;
        }
    }

    private function groupCheck() {

        //memberBased and groups
        if ($this->ignoreCon4GisRights) {
            return false;
        }
        if ($this->brickKey && $GLOBALS['con4gis']['groups']['installed'] &&
            (C4GBrickView::isMemberBased($this->viewType) ||
             C4GBrickView::isGroupBased($this->viewType))) {
            if (!MemberModel::hasRightInAnyGroup($this->User->id, $this->brickKey)) {
                $this->loadLanguageFiles();
                $return = array(
                    'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_PERMISSION_DENIED'],
                    'title' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_PERMISSION_DENIED_TITLE']
                );
                return json_encode($return);
            }
        }

        return false;
    }

    private function memberCheck($init = false) {
        if (FE_USER_LOGGED_IN) {
            \System::import('FrontendUser', 'User');
            $authenticated = $this->User->authenticate();

            if (!$authenticated && ($this->publicViewType) && (
                    ($this->viewType == C4GBrickViewType::PUBLICBASED) ||
                    ($this->viewType == C4GBrickViewType::PUBLICPARENTBASED)
                )) {
                $this->viewType = $this->publicViewType;
            }
        } else if (!C4GBrickView::isPublicBased($this->viewType) && !C4GBrickView::isPublicParentBased($this->viewType) && $GLOBALS['con4gis']['groups']['installed']) {
            $this->loadLanguageFiles();

            if ($init) {
                $this->initBrickModule(-1);
                if ($this->withPermissionCheck) {
                    $this->initPermissions();
                }
            }

            if ($this->dialogParams && $this->dialogParams->getViewParams()->getLoginRedirect()) {
                if ($this->dialogParams->getViewParams()->getLoginRedirect() && (($jumpTo = \PageModel::findByPk($this->dialogParams->getViewParams()->getLoginRedirect())) !== null)) {
                    $return['jump_to_url'] = $jumpTo->getFrontendUrl();
                    return json_encode($return);
                }
            }

            $return = array(
                'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_NOT_LOGGED_IN'],
                'title' => $GLOBALS['TL_LANG']['FE_C4G_LIST']['USERMESSAGE_NOT_LOGGED_IN_TITLE']
            );

            return json_encode($return);
        } else if (($this->publicViewType) && (
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
    private function loadLanguageFiles()
    {
        $language = $GLOBALS['TL_LANGUAGE'];
        \System::loadLanguageFile('fe_c4g_list',$language);
        \System::loadLanguageFile('fe_c4g_dialog',$language);

        if ($this->languageFile) {
            \System::loadLanguageFile($this->languageFile,$language);
        }
    }

    public function beforeAction($action)
    {
        //use it in your module class
    }

    public function initBrickModule($id)
    {
        //loading language files
        $this->loadLanguageFiles();

        if (FE_USER_LOGGED_IN) {
            \System::import('FrontendUser', 'User');
            $authenticated = $this->User->authenticate();
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
                $class      = new \ReflectionClass($this->entityClass);
                $namespace  = $class->getNamespaceName();
                $dbClass    = str_replace($namespace . '\\', '', $this->entityClass);
                $dbClass    = str_replace('\\', '', $dbClass);
            } else {
                $class      = new \ReflectionClass(get_called_class());
                $namespace  = str_replace("contao\\modules", "database", $class->getNamespaceName());
                $dbClass    = $this->modelClass;
            }

            $databaseParams->setFindBy($this->findBy);
            $databaseParams->setEntityNamespace($namespace);
            $databaseParams->setDatabase($this->Database);

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

        //setting list params
        //ToDo ViewType berücksichten (bei Formularen nicht notwendig)
        if (!$this->listParams) {
            $this->listParams = new C4GBrickListParams($this->brickKey, $this->viewType);
            $this->listParams->setWithModelListFunction(!empty($this->modelListFunction));
            //$this->listParams->setWithModelDialogFunction(!empty($this->modelDialogFunction));

            $groups = C4GBrickCommon::getGroupListForBrick($this->User->id, $this->brickKey);
            $groupCount = count($groups);
            $this->listParams->setGroupCount($groupCount);
            $this->listParams->setWithJQueryUI($this->strTemplate != 'mod_c4g_brick_simple');
            $this->listParams->setCaptionField($this->captionField);

            //$this->listParams->setProjectCount();
            //$this->listParams->setParentCount();
        }

        if (!$this->settings) {
            $settings = Database::getInstance()->execute("SELECT * FROM tl_c4g_settings LIMIT 1")->fetchAllAssoc();

            if ($settings) {
                $this->settings = $settings[0];
            }
        }

        //setting dialog params
        if (!$this->dialogParams) {
            $this->dialogParams = new C4GBrickDialogParams($this->brickKey, $this->viewType);
            $this->dialogParams->setGroupId($this->group_id);
            $this->dialogParams->setProjectId($this->project_id);
            $this->dialogParams->setProjectUuid($this->project_uuid);
            $this->dialogParams->setParentId($this->parent_id);
            $this->dialogParams->setMemberId($this->User->id);
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
            $contentId = $this->contentid;
            if (!$contentId) {
                $contentId = $this->settings['position_map'];
            }
            $this->dialogParams->setContentId($contentId);
            $this->dialogParams->setHeadline($this->headline);
            $this->dialogParams->setHeadlineTag($this->hl);
            $this->dialogParams->setSendEMails($this->sendEMails);
            $this->dialogParams->setWithNotification($this->withNotification);
            $this->dialogParams->setNotificationType($this->notification_type);
            $this->dialogParams->setNotificationTypeContactRequest($this->notification_type_contact_request);
            $this->dialogParams->setWithActivationInfo($this->withActivationInfo);
            $this->dialogParams->setPopup($this->isPopup);
            $this->dialogParams->setWithBackup($this->withBackup);
            $this->dialogParams->setRedirectBackSite($this->redirect_back_site);
            $this->dialogParams->setParentIdField($this->parentIdField);
            $this->dialogParams->setRedirectSite($this->redirect_site);
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
        \Session::getInstance()->set("c4g_brick_dialog_id", $id);
        if ($id) {
            $this->dialogParams->setId($id);
        }
        
        //Setting UUID
        if ($this->useUuidCookie) {
            if (!($_COOKIE[$this->UUID])) {
                if (!\Session::getInstance()->get($this->UUID)) {
                    \Session::getInstance()->set($this->UUID, C4GBrickCommon::getGUID());
                }
                setcookie($this->UUID, \Session::getInstance()->get($this->UUID), time() + 60 * 60 * 24 * 30, '/');
            } else {
                \Session::getInstance()->set($this->UUID, $_COOKIE[$this->UUID]);
                setcookie($this->UUID, ($_COOKIE[$this->UUID]), time() + 60 * 60 * 24 * 30, '/');
            }
            if (\Session::getInstance()->get($this->UUID)) {
                $this->dialogParams->setUuid(\Session::getInstance()->get($this->UUID));
                $this->listParams->setUuid(\Session::getInstance()->get($this->UUID));
            }

            //Synchronize MemberBased and PublicUuidBased view types
            if ((C4GBrickView::isMemberBased($this->viewType)) || (C4GBrickView::isPublicUUIDBased($this->viewType))) {
                if (($this->dialogParams->getMemberID() > 0) && ($this->dialogParams->getUuid())) {
                    $database = \Database::getInstance();
                    //in case the module table does not have a member_id field (otherwise an exception will be thrown and the site won't work)
                    $query = $database->prepare("SHOW COLUMNS FROM $this->tableName LIKE 'member_id'")->execute();
                    if ($query->numRows) {
                        $query = $database->prepare("SELECT * FROM " . $this->tableName .
                            " WHERE member_id = 0 AND uuid = '" . $this->dialogParams->getUuid() . "'")->execute();
                        if ($query) {
                            $stmt = $database->prepare("UPDATE " . $this->tableName . " SET member_id = " . $this->dialogParams->getMemberID()
                                . " WHERE member_id = 0 AND uuid = '" . $this->dialogParams->getUuid() . "'");
                            $stmt->execute();
                            if (($this->dialogParams->getMemberID() !== 0) && ($this->dialogParams->getMemberID() !== '0')) {
                                $stmt = $database->prepare("UPDATE " . $this->tableName . " SET uuid = '" . $this->dialogParams->getUuid()
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
            $putVars = \Session::getInstance()->get("c4g_brick_dialog_values");
            $result = $this->addFields();
            if ($result) {
                $this->fieldList = $result;
                $this->dialogChangeHandler = new C4GDialogChangeHandler();
                $this->fieldList = $this->dialogChangeHandler->reapplyChanges($this->brickKey, $this->fieldList);
            }
            if ($this->fieldList && $putVars) {
//                $this->setInitialValues($putVars);
            }
        }

//        if (!$this->User && ($this->viewType == C4GBrickViewType::PUBLICVIEW) || ($this->viewType == C4GBrickViewType::PUBLICPARENTVIEW)) {
//            foreach ($this->fieldList as $key=>$field) {
//                $this->fieldList[$key]->setEditable(false);
//            }
//        }


    }

    /**
     * @param $putVars
     */
    private function setInitialValues($putVars) {
        if ($this->fieldList && $putVars) {
            foreach ($this->fieldList as $field) {
                $value = $putVars[$field->getFieldName()];
                if ($value) {
                    $field->setInitialValue($value);
                }
            }
        }
    }

    /**
     * Display a wildcard in the back end
     * @return string
     */
    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.$this->name.' ###';
            $objTemplate->title = $this->headline;
            $objTemplate->hl = $this->hl;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        //ToDo Map über Schalter setzen

        if ($this->strTemplate == 'mod_c4g_brick_simple') {
            // initialize used Javascript Libraries and CSS files
            C4GJQueryGUI::initializeLibraries(
                true,                     // add c4gJQuery GUI Core LIB
                true,                     // add JQuery
                false,                     // add JQuery UI
                false,                    // add Tree Control
                true,                     // add Table Control
                false,                     // add history.js  //Ausgebaut der Mechanismus viele Nebeneffekte hat (Performance, JavaScript-Fehler, ...)
                true,                      // add simple tooltip
                true, 						  // $useMaps=false,
                false, 						  // $useGoogleMaps=false,
                true, 						  // $useMapsEditor=false,
                true, 						  // $useWswgEditor=false,
                true, 						  // $useScrollpane=false
                true                        //$usePopups
            );
        } else {
            // initialize used Javascript Libraries and CSS files
            C4GJQueryGUI::initializeLibraries(
                true,                     // add c4gJQuery GUI Core LIB
                true,                     // add JQuery
                true,                     // add JQuery UI
                false,                    // add Tree Control
                true,                     // add Table Control
                false,                     // add history.js  //Ausgebaut der Mechanismus viele Nebeneffekte hat (Performance, JavaScript-Fehler, ...)
                true,                      // add simple tooltip
                true, 						  // $useMaps=false,
                false, 						  // $useGoogleMaps=false,
                true, 						  // $useMapsEditor=false,
                true, 						  // $useWswgEditor=false,
                true, 						  // $useScrollpane=false
                true                        //$usePopups
            );


            $settings = Database::getInstance()->execute("SELECT * FROM tl_c4g_settings LIMIT 1")->fetchAllAssoc();

            if ($settings) {
                $this->settings = $settings[0];
            }

            // load custom themeroller-css if set
            if ($this->uiTheme) {
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $this->uiTheme;
            } else
            if ($this->c4g_appearance_themeroller_css) {
                $objFile = \FilesModel::findByUuid($this->c4g_appearance_themeroller_css);
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
            } else if($this->c4g_uitheme_css_select) {
                $theme = $this->c4g_uitheme_css_select;
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/ui-themes/themes/' . $theme . '/jquery-ui.css';
            } else if ($this->settings && $this->settings['c4g_appearance_themeroller_css']) {
                $objFile = \FilesModel::findByUuid($this->settings['c4g_appearance_themeroller_css']);
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
            } else if ($this->settings && $this->settings['c4g_uitheme_css_select']) {
                $theme = $this->settings['c4g_uitheme_css_select'];
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/ui-themes/themes/' . $theme . '/jquery-ui.css';
            } else {
                $GLOBALS['TL_CSS']['c4g_jquery_ui'] = 'bundles/con4giscore/vendor/jQuery/ui-themes/themes/base/jquery-ui.css';
            }
        }

        $GLOBALS['TL_CSS']['c4g_brick_style'] = 'bundles/con4gisprojects/css/c4g_brick.css';

        if ($this->brickStyle) {
            $GLOBALS['TL_CSS']['c4g_brick_style_'.$this->name] = $this->brickStyle;
        }
        if ($this->loadUrlClear) {
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4gisprojects/js/ClearBrowserUrl.js';
        }

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4gisprojects/js/C4GBrickDialog.js';

        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4gisprojects/js/ConditionalFieldDisplay.js';
        // load more button
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4gisprojects/js/more-button.js';
        $GLOBALS['TL_JAVASCRIPT'][] = "bundles/con4giscore/vendor/fontawesome/js/all.js";

        if ($this->brickScript) {
            $GLOBALS['TL_JAVASCRIPT']['c4g_brick_script_'.$this->name] = $this->brickScript;
        }
        // load the js file for triggering the search from another module
        ResourceLoader::loadJavaScriptDeferred("datatable-search-trigger", "bundles/con4gisprojects/js/datatable-search-trigger.js");

        $data['jquiEmbeddedDialogs'] = true;//$this->dialogs_jqui;
        $GLOBALS['TL_CSS'] [] = 'bundles/con4giscore/vendor/wswgEditor/css/editor.css';
        $GLOBALS['TL_CSS'] [] = 'bundles/con4giscore/vendor/wswgEditor/css/bbcodes.css';
        $GLOBALS['TL_CSS']['simple-dtpicker'] = 'bundles/con4giscore/vendor/jQuery/plugins/jquery-simple-datetimepicker/1.13.0/jquery.simple-dtpicker.css';

        //toolbar buttons for ckEditor
        $aToolbarButtons = [
            'Cut',
            'Copy',
            'Paste',
            'PasteText',
            'PasteFromWord',
            '-',
            'Undo',
            'Redo',
            'Bold',
            'Italic',
            'Underline',
            'Strike',
            'Subscript',
            'Superscript',
            'Blockquote',
            '-',
            'RemoveFormat',
            'NumberedList',
            'BulletedList',
            'Link',
            'Unlink',
            'Anchor',
            'Image',
            'FileUpload',
            'Smiley',
            'TextColor',
            'BGColor'
        ];

        $GLOBALS['TL_CSS'][]        = 'bundles/con4giscore/vendor/jQuery/plugins/chosen/chosen.css';
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4giscore/vendor/jQuery/plugins/chosen/chosen.jquery.min.js';

        $GLOBALS['TL_HEAD'][] = "<script>var ckRemovePlugins = 'bbcode';</script>";
        $GLOBALS['TL_HEAD'][] = "<script>var uploadApiUrl = 'con4gis/api/fileUpload/';</script>";

        $foundHeadlement = false;
        foreach ($GLOBALS['TL_HEAD'] as $key=>$headlement) {
            if (strpos($headlement, "var ckEditorItems") > 0) {
               $foundHeadlement = true;
               $GLOBALS['TL_HEAD'][$key] = "<script>var ckEditorItems = ['" . implode("','", $aToolbarButtons) . "'];</script>";
               break;
            }
        }

        if (!$foundHeadlement) {
            $GLOBALS['TL_HEAD'][] = "<script>var ckEditorItems = ['" . implode("','", $aToolbarButtons) . "'];</script>";
            $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/con4giscore/vendor/ckeditor/ckeditor.js';
        }

        $data['id']         = $this->id;
        $data['ajaxUrl']    = "con4gis/brick_ajax_api";
        $data['ajaxData']   = $this->id;
        $data['height']     = 'auto';
        $data['width']      = '100%';
        $data['embedDialogs'] = true;

        if (($_SERVER['REQUEST_METHOD']) == 'PUT') {
            parse_str(file_get_contents("php://input"),$this->putVars);
        }

        //ToDo Wird der state hier wirklich gebraucht?
        if ($_GET['state']) {
            $request = $_GET['state'];
        }
        else {
            $request = 'initnav';
        }

        if ($request == 'undefined') {
            $request = C4GBrickActionType::IDENTIFIER_LIST.':-1';
        }
        if ($this->asnycList) {
            $arrAction = [];
            $arrAction['initAction'] = "C4GShowListAction:-1";
            $data['initData'] = json_encode($arrAction);
        } else {
            $initData = $this->generateAjax($request);
            if ($initData && count(json_decode($initData)) > 0) {
                $data['initData'] = $initData;
            } else {
                $initData = $this->generateAjax(C4GBrickActionType::IDENTIFIER_LIST.':-1');
                $data['initData'] = $initData;
            }
        }



        $data['div'] = 'c4g_brick';

        $this->Template->c4gData = $data;
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
                $session = $this->Session->getData();
                $session['referer']['last'] = $session['referer']['current'];
                // echo '<br>[LAST]' . $session['referer']['last'] . '<br>';
                $session['referer']['current'] = C4GUtils::addParametersToURL(
                    $session['referer']['last'],
                    array('state' => $request));
                // echo '<br>[CURRENT]' . $session['referer']['current'] . '<br>';
                $this->Session->setData($session);
            }
        }

        //ToDo prüfen ob der Try/Catch Block hier immer noch hilfreich ist.
        try {
            $result = $this->memberCheck(true);
            if ($result) {
                return $result;
            }

            $result = $this->groupCheck();
            if ($result) {
                return $result;
            }

            $session = $this->Session->getData();

            if (FE_USER_LOGGED_IN) {
                \System::import('FrontendUser', 'User');
                $authenticated = $this->User->authenticate();
            }

            if (C4GBrickView::isGroupBased($this->viewType)){
                    if (($this->group_id == -1) || ($this->group_id == null)) {
                        $groupId = \Session::getInstance()->get("c4g_brick_group_id");
                        if ($groupId && MemberGroupModel::isMemberOfGroup($groupId, $this->User->id)) {
                            if (MemberModel::hasRightInGroup($this->User->id, $groupId, $this->brickKey)) {
                                $this->group_id = $groupId;
                            }
                        }

                        C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                        C4GBrickCommon::mkdir(C4GBrickConst::PATH_GROUP_DATA);

                        $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id;
                        C4GBrickCommon::mkdir($path);
                    }
            } else if (C4GBrickView::isProjectBased($this->viewType)) {
                if (($this->group_id == -1) || ($this->group_id == null)) {
                    $groupId = \Session::getInstance()->get("c4g_brick_group_id");
                    if (C4GBrickAction::checkGroupId($groupId, $this->User->id, $this->brickKey)) {
                        $this->group_id = $groupId;
                    }

                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_GROUP_DATA);

                    $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id;
                    C4GBrickCommon::mkdir($path);
                }

                if (($this->projectCount == -1) || ($this->projectCount == null)) {
                    $this->projectCount = count(C4gProjectsModel::getProjectListForBrick( $this->User->id, $this->group_id, $this->projectKey ));
                }

                if (($this->project_id == -1) || ($this->project_id == null)) {
                    $project_id = \Session::getInstance()->get("c4g_brick_project_id");
                    if (C4gProjectsModel::checkProjectId($project_id, $this->projectKey)) {
                        $this->project_id = $project_id;
                        $this->project_uuid = \Session::getInstance()->get("c4g_brick_project_uuid");
                        $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id . '/' . $this->project_uuid;
                        C4GBrickCommon::mkdir($path);
                    }
                }
            } else if (C4GBrickView::isProjectParentBased($this->viewType)) {
                if (($this->group_id == -1) || ($this->group_id == null)) {
                    $groupId = \Session::getInstance()->get("c4g_brick_group_id");
                    if (C4GBrickAction::checkGroupId($groupId, $this->User->id, $this->brickKey)) {
                        $this->group_id = $groupId;
                    }

                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                    C4GBrickCommon::mkdir(C4GBrickConst::PATH_GROUP_DATA);

                    $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id;
                    C4GBrickCommon::mkdir($path);
                }

                if (($this->projectCount == -1) || ($this->projectCount == null)) {
                    $this->projectCount = count(C4gProjectsModel::getProjectListForBrick($this->User->id, $this->group_id, $this->projectKey));
                }

                if (($this->project_id == -1) || ($this->project_id == null)) {
                    $project_id = \Session::getInstance()->get("c4g_brick_project_id");
                    if (C4gProjectsModel::checkProjectId($project_id, $this->projectKey)) {
                        $this->project_id = $project_id;
                        $this->project_uuid = \Session::getInstance()->get("c4g_brick_project_uuid");
                        $path = C4GBrickConst::PATH_GROUP_DATA . '/' . $this->group_id . '/' . $this->project_uuid;
                        C4GBrickCommon::mkdir($path);
                    }
                }

                if (($this->parent_id == -1) || ($this->parent_id == null)) {
                    $this->parent_id = \Session::getInstance()->get("c4g_brick_parent_id");
                }
            } else if (C4GBrickView::isMemberBased($this->viewType)) {


                C4GBrickCommon::mkdir(C4GBrickConst::PATH_BRICK_DATA);
                C4GBrickCommon::mkdir(C4GBrickConst::PATH_MEMBER_DATA);

                $path = C4GBrickConst::PATH_MEMBER_DATA . '/' . $this->User->id;
                C4GBrickCommon::mkdir($path);

                //Hier wird nur die UserId benötigt und die steht überall zur Verfügung.
            }

            $this->frontendUrl = $this->Environment->url . TL_PATH . '/' . $session['referer']['current'];

            if (($_SERVER['REQUEST_METHOD']) == 'PUT') {
                parse_str(file_get_contents("php://input"), $this->putVars);
                foreach ($this->putVars as $key => $putVar) {
                    $tmpVar = C4GUtils::secure_ugc($putVar);
                    $tmpVar = C4GUtils::cleanHtml($tmpVar);
                    $this->putVars[$key] = $tmpVar;
                }
            }

            // if there was an initial get parameter "state" then use it for jumping directly
            // to the refering function
            if (($request == 'initnav') && $_GET['initreq']) {
                $_GET['historyreq'] = $_GET['initreq'];
            }

            //permalink (1 permalink=id / 2 fieldname=id / 3 permalink_name=id)
            if ($this->permalink_field &&
                    (   $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK] ||
                        $_GET[$this->permalink_field] ||
                        ($this->permalink_name && $_GET[$this->permalink_name])
                    )
            ) {
                if (!$this->brickDatabase) {
                    $this->initBrickModule(-1);
                    if ($this->withPermissionCheck) {
                        $this->initPermissions();
                    }
                }

                if ($_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]) {
                    if (!$this->permalinkModelClass) {
                        $dataset = $this->brickDatabase->findBy($this->permalink_field, $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]);
                    } else {
                        $model = $this->permalinkModelClass;
                        $dataset = $model::findBy($this->permalink_field, $_GET[C4GBrickActionType::IDENTIFIER_PERMALINK]);
                    }

                    if ($dataset) {
                        $id = $dataset->id;
                        $this->initBrickModule($id);
                        if ($this->withPermissionCheck) {
                            $this->initPermissions();
                        }
                        $action = C4GBrickActionType::IDENTIFIER_LIST.':'.$id;
                        $result = $this->performAction($action);
                    }
                } else if ($_GET[$this->permalink_field]) {
                    if (!$this->permalinkModelClass) {
                        $dataset = $this->brickDatabase->findBy($this->permalink_field, $_GET[$this->permalink_field]);
                    } else {
                        $model = $this->permalinkModelClass;
                        $dataset = $model::findBy($this->permalink_field, $_GET[$this->permalink_field]);
                    }

                    if ($dataset) {
                        $id = $dataset->id;
                        $this->initBrickModule($id);
                        if ($this->withPermissionCheck) {
                            $this->initPermissions();
                        }
                        $action = C4GBrickActionType::IDENTIFIER_LIST.':'.$id;
                        $result = $this->performAction($action);
                    }
                } else if ($this->permalink_name && $_GET[$this->permalink_name]) {
                    if (!$this->permalinkModelClass) {
                        $dataset = $this->brickDatabase->findBy($this->permalink_field, $_GET[$this->permalink_name]);
                    } else {
                        $model = $this->permalinkModelClass;
                        $dataset = $model::findBy($this->permalink_field, $_GET[$this->permalink_name]);
                    }
                    if ($dataset) {
                        $id = $dataset->id;
                        $this->initBrickModule($id);
                        if ($this->withPermissionCheck) {
                            $this->initPermissions();
                        }
                        $action = C4GBrickActionType::IDENTIFIER_LIST.':'.$id;
                        $result = $this->performAction($action);

                    }
                }
                // History navigation
            } else if ($_GET['historyreq']) {
                $actions = explode(';', $_GET['historyreq']);
                $result = array();
                foreach ($actions AS $action) {
                    $r = $this->performHistoryAction($action);
                    array_insert($result, 0, $r);
                }

            } else {
                switch ($request) {
                    case 'initnav' :
                        $action = C4GBrickActionType::IDENTIFIER_LIST.':-1';
                        $result = $this->performAction($action);
                        break;
                    default:
                        $actions = explode(';', $request);
                        if (strpos($actions[0],':') === false) {
                            //Formulardialog
                            $pos = strpos($actions[0],C4GBrickActionType::IDENTIFIER_DIALOG);
                            if ( ($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_DIALOG));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_DIALOG.':'.$id;
                            }
                            $pos = strpos($actions[0],C4GBrickActionType::IDENTIFIER_BRICKDIALOG);
                            if ( ($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_BRICKDIALOG));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_BRICKDIALOG.':'.$id;
                            }

                            //Messagedialog
                            $pos = strpos($actions[0], C4GBrickActionType::IDENTIFIER_MESSAGE);
                            if ( ($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_MESSAGE));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_MESSAGE.':'.$id;
                            }

                            //Selectdialog
                            $pos = strpos($actions[0], C4GBrickActionType::IDENTIFIER_SELECT);
                            if ( ($pos !== false) && ($pos == 0)) {
                                $id = substr($actions[0], strlen(C4GBrickActionType::IDENTIFIER_SELECT));
                                $actions[0] = C4GBrickActionType::IDENTIFIER_SELECT.':'.$id;
                            }
                        }

                        /*if ( ($actions[0] == '-1') ) {
                          $actions[0] = C4GBrickActionType::IDENTIFIER_LIST; //tritt evtl. auf, wenn das Browserfenster neu geladen wird, so greiftn default in performAction
                        }*/

                        $result = array();
                        foreach ($actions AS $action) {
                            $r = $this->performAction($action);
                            if (is_array($r)) {
                                $result = array_merge($result, $r);
                            }/* else {
                                if (strpos($action, 'savedialog:') !== false) {
                                    $this->initBrickModule(\Session::getInstance()->get("c4g_brick_dialog_id"));
                                }
                            }*/
                        }
                }
            }
        } catch (Exception $e) {
            $result = $this->showException($e);
        }

        return json_encode($result);
    }

    /**
     * module event controller
     *
     * @param $action
     * @return array|mixed
     */
    private function performAction ($action, $withMemberCheck=true) {
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
                $putVars = \Session::getInstance()->get("c4g_brick_dialog_values");
            }

            foreach ($this->fieldList as $field) {
                $fieldName = $field->getFieldName();
                $putVars[$fieldName] = $field->validateFieldValue($putVars[$fieldName]);
            }

            $result = $this->$function($values, $putVars);
            return $result;
        } else {
            $this->beforeAction($action);
            return C4GBrickAction::performAction($action, $this);
        }

    }


    /**
     * Initialize C4GPermissions for this module.
     * TODO ist final hier nicht überflüssig, da die Methode sowieso private ist und in der Kindklasse nicht sichtbar ist?
     */
    private final function initPermissions()
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
        $array = array();
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
        if (sizeof($array) > 0) {
            $result = new C4GTablePermission($this->getC4GTablePermissionTable(), $array);
            return $result;
        } else {
            return null;
        }
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
     * perform a history action
     * @param string $historyAction
     */
    public function performHistoryAction($historyAction)
    {
        $values = explode(':', $historyAction);
        $this->action = $values[0];

        $result = $this->performAction($historyAction);

        // close all dialogs that have been open to avoid conflicts
        $result['dialogcloseall'] = true;

        return $result;
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
    protected function getBrickSelect() {
        return null;
    }

    protected function setBrickCaptions($singular, $plural) {
        $this->brickCaption = $singular;
        $this->brickCaptionPlural = $plural;

        if ($this->dialogParams) {
            $this->dialogParams->setBrickCaption($singular);
            $this->dialogParams->setBrickCaptionPlural($plural);
        }
    }

    protected function setParentCaptions($singular, $plural) {
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
}
