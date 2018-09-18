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

    //IDENTIFIER ACTIONS
    const IDENTIFIER_LIST_ACTION = 'C4GIdentifierListAction';

    //TABLE DIALOG IDENTIFIER
    const IDENTIFIER_DIALOG_ACTION = 'C4GShowDialogAction';
    const IDENTIFIER_PARENT_ACTION = 'C4GIdentifierParentAction';
    const IDENTIFIER_MESSAGE_ACTION = 'C4GShowMessageDialogAction';

    //MESSAGE DIALOG ACTIONS

    //TABLE DIALOG ACTIONS
    const ACTION_SHOWDIALOG   = 'C4GShowDialogAction';
    const ACTION_DELETEDIALOG = 'C4GDeleteDialogAction';
    const ACTION_ARCHIVEDIALOG = 'C4GArchiveDialogAction';
    const ACTION_EMAILNOTIFICATIONDIALOG = 'C4GShowEmailNotificationDialogAction';
    const ACTION_SEND_NOTIFICATION = 'C4GSendNotificationAction';
    const ACTION_ACTIVATIONDIALOG = 'C4GActivationDialogAction';
    const ACTION_FREEZEDIALOG = 'C4GFreezeDialogAction';
    const ACTION_DEFROSTDIALOG = 'C4GDefrostDialogAction';
    const ACTION_CLOSEDIALOG  = 'C4GCloseDialogAction';
    const ACTION_SAVEDIALOG   = 'C4GSaveDialogAction';
    const ACTION_SAVEANDNEWDIALOG   = 'C4GSaveAndNewDialogAction';
    const ACTION_SAVEANDREDIRECTDIALOG   = 'C4GSaveAndRedirectDialogAction';
    const ACTION_TICKET = 'C4GTicketDialogAction';
    const ACTION_SEARCH = 'search'; //Todo Unused. Delete?
    const ACTION_EXPORT = 'C4GExportDialogAction';
    const ACTION_PRINT  = 'C4GPrintDialogAction';
    const ACTION_POPUP  = 'popup'; //Todo Unused. Delete?
    const ACTION_REDIRECT = 'C4GRedirectAction';
    const ACTION_REDIRECT_TO_DETAIL = 'C4GRedirectDetailAction';   //Todo new Action
    const ACTION_REDIRECTBACK = 'C4GRedirectBackAction';
    const ACTION_REDIRECTDIALOGACTION = 'C4GRedirectDialogAction';
    const ACTION_SHOWANIMATION = 'animation'; //Todo Unused. Delete?
    const ACTION_BUTTONCLICK   = 'buttonclick'; //Todo Does this not have an action?
    const ACTION_RELOAD  = 'C4GReloadAction';
    const ACTION_RESTART = 'C4GReloadAction'; //timer //Todo new Action
    const ACTION_LOGINREDIRECT = 'C4GLoginRedirectAction';

    const ACTION_SHOWMESSAGEDIALOG = 'C4GShowMessageDialogAction';
    const ACTION_CONFIRMDELETE = 'C4GConfirmDeleteAction';
    const ACTION_CONFIRMMESSAGE = 'C4GConfirmMessageAction';
    const ACTION_CANCELMESSAGE = 'C4GCancelDialogAction';
    const ACTION_CONFIRMARCHIVE = 'C4GConfirmArchiveAction';
    const ACTION_CANCELARCHIVE = 'C4GCancelDialogAction';
    const ACTION_CONFIRMEMAILNOTIFICATION = 'C4GSendEmailNotificationAction';
    const ACTION_CANCELEMAILNOTIFICATION = 'C4GCancelDialogAction';
    const ACTION_CONFIRMACTIVATION = 'C4GConfirmActivationAction';
    const ACTION_CANCELACTIVATION = 'C4GCancelDialogAction';
    const ACTION_CONFIRMFREEZE = 'C4GConfirmFreezeAction';
    const ACTION_CANCELFREEZE = 'C4GCancelDialogAction';
    const ACTION_CONFIRMDEFROST = 'C4GConfirmDefrostAction';
    const ACTION_CANCELDEFROST = 'C4GCancelDialogAction';
    const ACTION_CLOSEPOPUPDIALOG = 'C4GClosePopupDialogAction'; //Todo Empty Class
    const ACTION_CHANGEFIELD    = 'C4GChangeFieldAction';

    //TABLE LIST ACTIONS
    const ACTION_CLICK = 'C4GShowDialogAction';
    const ACTION_SELECTGROUP    = 'C4GSelectGroupDialogAction';
    const ACTION_SELECTPROJECT  = 'C4GSelectProjectDialogAction';
    const ACTION_SELECTPARENT   = 'C4GSelectParentDialogAction';
    const ACTION_SELECTPUBLICPARENT   = 'C4GSelectPublicParentDialogAction';
    const ACTION_FILTER         = 'C4GShowFilterDialogAction';
    const ACTION_PRINTLIST      = 'printlist'; //Todo Unused. Delete?
    const ACTION_IMPORT         = 'import'; //Todo Unused. Delete?

    //SELECT DIALOG ACTIONS
    const ACTION_CONFIRMSELECT          = 'confirmselect'; //Todo Unused. Delete?
    const ACTION_CANCELSELECT           = 'cancelselect'; //Todo Unused. Delete?
    const ACTION_CONFIRMGROUPSELECT     = 'C4GConfirmGroupSelectAction';
    const ACTION_CANCELGROUPSELECT      = 'C4GCancelDialogAction';
    const ACTION_CONFIRMPROJECTSELECT   = 'C4GSetProjectIdAction';
    const ACTION_CANCELPROJECTSELECT    = 'C4GCancelDialogAction';
    const ACTION_CONFIRMPARENTSELECT    = 'C4GSetParentIdAction';
    const ACTION_CANCELPARENTSELECT     = 'C4GCancelDialogAction';
    const ACTION_CONFIRMPARENTFILTER    = 'C4GSetFilterAction';
    const ACTION_CANCELPARENTFILTER     = 'C4GShowListAction';
}