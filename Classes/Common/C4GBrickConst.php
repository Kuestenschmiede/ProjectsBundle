<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Common;

class C4GBrickConst {
    //Classes
    const CLASS_DIALOG = 'c4g_brick_dialog';
    const CLASS_TILES  = 'c4g_brick_tiles';
    const CLASS_LIST   = 'c4g_brick_list';

    const CLASS_MESSAGE_DIALOG   = 'c4g_brick_message_dialog';
    const CLASS_SELECT_DIALOG    = 'c4g_brick_select_dialog';
    const CLASS_REDIRECT_DIALOG  = 'c4g_brick_redirect_dialog';
    const CLASS_FILTER_DIALOG    = 'c4g_brick_filter_dialog';

    //PATHES
    const PATH_BRICK_DATA        = 'files/c4g_brick_data';
    const PATH_GROUP_DATA        = 'files/c4g_brick_data/group';
    const PATH_MEMBER_DATA       = 'files/c4g_brick_data/member';
    const PATH_BRICK_DOCUMENTS   = 'files/c4g_brick_data/documents';

    //LIST BUTTON
    const BUTTON_ADD       = 'CREATEELEMENT';
//    const BUTTON_EDIT      = 'EDITELEMENT';
//    const BUTTON_DELETE    = 'DELETEELEMENT';
    const BUTTON_GROUP     = 'SELECTGROUP';
    const BUTTON_PROJECT   = 'SELECTPROJECT';
    const BUTTON_PARENT    = 'SELECTPARENT';
    const BUTTON_PUBLIC_PARENT    = 'SELECTPUBLICPARENT';
    const BUTTON_FILTER    = 'SELECTFILTER';
    const BUTTON_TOGGLE_METHOD_FILTER    = 'SELECTTOGGLEMETHODFILTER';
    const BUTTON_RESET_PARENT = 'RESETPARENT';
    const BUTTON_IMPORT    = 'SELECTIMPORT';
    const BUTTON_PRINTLIST = 'PRINTLIST';

    //DIALOG BUTTON
    const BUTTON_SAVE            = 'SAVE';
    const BUTTON_SAVE_AND_NEW    = 'SAVE_AND_NEW';
    const BUTTON_SAVE_AND_REDIRECT = 'SAVE_AND_REDIRECT';
    const BUTTON_TICKET          = 'TICKET';
    const BUTTON_BOOKING_SAVE    = 'BOOKING_SAVE';
    const BUTTON_BOOKING_CHANGE  = 'BOOKING_CHANGE';
    const BUTTON_ARCHIVE         = 'ARCHIVE';
    const BUTTON_ACTIVATION      = 'ACTIVATION';
    const BUTTON_FREEZE          = 'FREEZE';
    const BUTTON_SEND_EMAIL      = 'SEND_EMAIL';
    const BUTTON_SEND_NOTIFICATION = 'SEND_NOTIFICATION';
    const BUTTON_DEFROST         = 'DEFROST';
    const BUTTON_DELETE          = 'DELETE';
    const BUTTON_CANCEL          = 'CANCEL';
    const BUTTON_EXPORT          = 'EXPORT';
    const BUTTON_PRINT           = 'PRINT';
    const BUTTON_POPUP           = 'POPUP';
    const BUTTON_REDIRECT        = 'REDIRECT';
    const BUTTON_REDIRECT_BACK   = 'REDIRECTBACK';
    const BUTTON_CLICK           = 'CLICK';
    const BUTTON_NEXT            = 'NEXT';

    //REDIRECT TYPES
    const REDIRECT_DEFAULT       = 'DEFAULT';
    const REDIRECT_PROJECT       = 'PROJECT';
    const REDIRECT_GROUP         = 'GROUP';
    const REDIRECT_PARENT        = 'PARENT';

    //OVERLAY TYPES
    const OVERLAY_DIALOG         = 'OVERLAY_DIALOG';
    const OVERLAY_ANIMATION      = 'OVERLAY_ANIMATION';

    const ONCLICK_TYPE_SERVER    = 'PHP';
    const ONCLICK_TYPE_CLIENT    = 'JS';

    //ID TYPES
    const ID_TYPE_MEMBER  = 'MEMBER';
    const ID_TYPE_GROUP   = 'GROUP';
    const ID_TYPE_PROJECT = 'PROJECT';
    const ID_TYPE_PARENT  = 'PARENT';

    //INCLUDE TYPES
    const INCLUDE_ARTICLE = 'ARTICLE';
    const INCLUDE_CONTENT = 'CONTENT';
    const INCLUDE_MODULE  = 'MODULE';
    const INCLUDE_FORM    = 'FORM';
    const INCLUDE_NEWS    = 'NEWS';
    const INCLUDE_EVENT   = 'EVENT';
}
