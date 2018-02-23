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

namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GBrickActionType
{
    //TABLE LIST IDENTIFIER
    const IDENTIFIER_LIST = 'list';

    //TABLE DIALOG IDENTIFIER
    const IDENTIFIER_DIALOG = 'brickdialog';
    const IDENTIFIER_PARENT = 'brickparent';
    const IDENTIFIER_PROJECT = 'brickproject';
    const IDENTIFIER_MESSAGE = 'brickmessage';
    const IDENTIFIER_SELECT = 'brickselect';
    const IDENTIFIER_REDIRECT = 'brickredirect';
    const IDENTIFIER_FILTER = 'brickfilter';
    const IDENTIFIER_PERMALINK = 'permalink';

    //MESSAGE DIALOG ACTIONS

    //TABLE DIALOG ACTIONS
    const ACTION_SHOWDIALOG   = 'showdialog';
    const ACTION_DELETEDIALOG = 'deletedialog';
    const ACTION_ARCHIVEDIALOG = 'archivedialog';
    const ACTION_EMAILNOTIFICATIONDIALOG = 'emailnotificationdialog';
    const ACTION_SEND_NOTIFICATION = 'sendnotification';
    const ACTION_ACTIVATIONDIALOG = 'activationdialog';
    const ACTION_FREEZEDIALOG = 'freezedialog';
    const ACTION_DEFROSTDIALOG = 'defrostdialog';
    const ACTION_CLOSEDIALOG  = 'closedialog';
    const ACTION_SAVEDIALOG   = 'savedialog';
    const ACTION_SAVEANDNEWDIALOG   = 'saveandnewdialog';
    const ACTION_SAVEANDREDIRECTDIALOG   = 'saveandredirectdialog';
    const ACTION_TICKET = 'ticket';
    const ACTION_SEARCH = 'search';
    const ACTION_EXPORT = 'export';
    const ACTION_PRINT  = 'print';
    const ACTION_POPUP  = 'popup';
    const ACTION_REDIRECT = 'redirect';
    const ACTION_REDIRECT_TO_DETAIL = 'redirecttodetail';
    const ACTION_REDIRECTBACK = 'redirectback';
    const ACTION_REDIRECTDIALOGACTION = 'redirectdialog';
    const ACTION_SHOWANIMATION = 'animation';
    const ACTION_BUTTONCLICK   = 'buttonclick';
    const ACTION_RELOAD  = 'reload';
    const ACTION_RESTART = 'reload'; //timer
    const ACTION_LOGINREDIRECT = 'loginredirect';

    const ACTION_SHOWMESSAGEDIALOG = 'showmessage';
    const ACTION_CONFIRMDELETE = 'confirmdelete';
    const ACTION_CONFIRMMESSAGE = 'confirmmessage';
    const ACTION_CANCELMESSAGE = 'cancelmessage';
    const ACTION_CONFIRMARCHIVE = 'confirmarchive';
    const ACTION_CANCELARCHIVE = 'cancelarchive';
    const ACTION_CONFIRMEMAILNOTIFICATION = 'confirmemailnotification';
    const ACTION_CANCELEMAILNOTIFICATION = 'cancelemailnotification';
    const ACTION_CONFIRMACTIVATION = 'confirmactivation';
    const ACTION_CANCELACTIVATION = 'cancelactivation';
    const ACTION_CONFIRMFREEZE = 'confirmfreeze';
    const ACTION_CANCELFREEZE = 'cancelfreeze';
    const ACTION_CONFIRMDEFROST = 'confirmdefrost';
    const ACTION_CANCELDEFROST = 'canceldefrost';
    const ACTION_CLOSEPOPUPDIALOG = 'closepopup';
    const ACTION_CHANGEFIELD    = 'changefield';

    //TABLE LIST ACTIONS
    const ACTION_CLICK = 'click';
    const ACTION_SELECTGROUP    = 'selectgroup';
    const ACTION_SELECTPROJECT  = 'selectproject';
    const ACTION_SELECTPARENT   = 'selectparent';
    const ACTION_FILTER         = 'filter';
    const ACTION_PRINTLIST      = 'printlist';
    const ACTION_IMPORT         = 'import';

    //SELECT DIALOG ACTIONS
    const ACTION_CONFIRMSELECT          = 'confirmselect';
    const ACTION_CANCELSELECT           = 'cancelselect';
    const ACTION_CONFIRMGROUPSELECT     = 'confirmgroupselect';
    const ACTION_CANCELGROUPSELECT      = 'cancelgroupselect';
    const ACTION_CONFIRMPROJECTSELECT   = 'confirmprojectselect';
    const ACTION_CANCELPROJECTSELECT    = 'cancelprojectselect';
    const ACTION_CONFIRMPARENTSELECT    = 'confirmparentselect';
    const ACTION_CANCELPARENTSELECT     = 'cancelparentselect';
    const ACTION_CONFIRMPARENTFILTER    = 'confirmbrickfilter';
    const ACTION_CANCELPARENTFILTER     = 'cancelparentfilter';
}