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
namespace con4gis\ProjectBundle\Classes\Common;

class C4GBrickConst {
    //Classes
    const CLASS_DIALOG = 'c4g_brick_dialog';
    const CLASS_TILES  = 'c4g_brick_tiles';
    const CLASS_MESSAGE_DIALOG = 'c4g_brick_message_dialog';
    const CLASS_SELECT_DIALOG   = 'c4g_brick_select_dialog';
    const CLASS_FILTER_DIALOG = 'c4g_brick_filter_dialog';

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
    const BUTTON_FILTER    = 'SELECTFILTER';
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
    const BUTTON_NEXT                   = 'NEXT';


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
}
