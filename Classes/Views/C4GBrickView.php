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
namespace con4gis\ProjectBundle\Classes\Views;

class C4GBrickView {

    /**
     * @return boolean
     */
    public static function isPublicBased($viewType)
    {
        switch($viewType) {
            case C4GBrickViewType::PUBLICVIEW:
            case C4GBrickViewType::PUBLICBASED:
            case C4GBrickViewType::PUBLICFORM:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isGroupBased($viewType)
    {
        switch($viewType) {
            case C4GBrickViewType::GROUPPROJECT:
            case C4GBrickViewType::GROUPPARENTVIEW:
            case C4GBrickViewType::GROUPPARENTBASED:
            case C4GBrickViewType::GROUPBASED:
            case C4GBrickViewType::GROUPVIEW:
            case C4GBrickViewType::GROUPFORM:
            case C4GBrickViewType::GROUPFORMCOPY:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isMemberBased($viewType)
    {
        switch ($viewType) {
            case C4GBrickViewType::MEMBERBASED:
            case C4GBrickViewType::MEMBERBOOKING:
            case C4GBrickViewType::MEMBERVIEW:
            case C4GBrickViewType::MEMBERFORM:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isProjectBased($viewType)
    {
        switch($viewType) {
            case C4GBrickViewType::PROJECTBASED:
            case C4GBrickViewType::PROJECTFORM:
            case C4GBrickViewType::PROJECTFORMCOPY:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isProjectParentBased($viewType)
    {
        switch ($viewType) {
            case C4GBrickViewType::PROJECTPARENTBASED:
            case C4GBrickViewType::PROJECTPARENTVIEW:
            case C4GBrickViewType::PROJECTPARENTFORM:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isGroupParentBased($viewType)
    {
        switch ($viewType) {
            case C4GBrickViewType::GROUPPARENTBASED:
            case C4GBrickViewType::GROUPPARENTVIEW:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isWithoutList($viewType)
    {
        switch($viewType) {
            case C4GBrickViewType::GROUPFORM:
            case C4GBrickViewType::GROUPFORMCOPY:
            case C4GBrickViewType::MEMBERFORM:
            case C4GBrickViewType::PUBLICFORM:
            case C4GBrickViewType::PROJECTFORM:
            case C4GBrickViewType::PROJECTFORMCOPY:
            case C4GBrickViewType::PROJECTPARENTFORMCOPY:
                return true;
        }

        return false;
    }

    /**
     * @return boolean
     */
    public static function isWithoutEditing($viewType)
    {
        switch($viewType) {
            case C4GBrickViewType::GROUPPARENTVIEW:
            case C4GBrickViewType::GROUPVIEW:
            case C4GBrickViewType::MEMBERVIEW:
            case C4GBrickViewType::PROJECTPARENTVIEW:
            case C4GBrickViewType::PUBLICVIEW:
            case C4GBrickViewType::PUBLICBASED: //ToDo check if this viewType is really without editing
                return true;
        }

        return false;
    }

    public static function isWithMember($viewType) {
        if (C4GBrickView::isMemberBased($viewType)) {
            return true;
        }

        return false;
    }

    /**
     * @param $viewType
     * @return bool
     */
    public static function isWithGroup($viewType) {
        if (C4GBrickView::isGroupBased($viewType) ||
            C4GBrickView::isProjectBased($viewType) ||
            C4GBrickView::isProjectParentBased($viewType) ||
            C4GBrickView::isGroupParentBased($viewType)) {
            return true;
        }
        return false;
    }

    /**
     * @param $viewType
     * @return bool
     */
    public static function isWithProject($viewType)
    {

        if (C4GBrickView::isProjectBased($viewType) ||
            C4GBrickView::isProjectParentBased($viewType)) {
            return true;
        }

        return false;
    }

    /**
     * @param $viewType
     * @return bool
     */
    public static function isWithParent($viewType)
    {

        if (C4GBrickView::isProjectParentBased($viewType) ||
            C4GBrickView::isGroupParentBased($viewType)) {
            return true;
        }

        return false;
    }

    /**
     * @param $viewType
     * @return bool
     */
    public static function isWithSaving($viewType) {
        if ((C4GBrickView::isPublicBased($viewType) ||
            C4GBrickView::isGroupBased($viewType) ||
            C4GBrickView::isProjectBased($viewType) ||
            C4GBrickView::isMemberBased($viewType) ||
            C4GBrickView::isProjectParentBased($viewType) ||
            C4GBrickView::isGroupParentBased($viewType)) && (!C4GBrickView::isWithoutEditing($viewType))) {
            return true;
        }

        return false;
    }

    /**
     * @param $viewType
     * @return bool
     */
    public static function isFormular($viewType) {
        if (($viewType == C4GBrickViewType::GROUPFORM) ||
            ($viewType == C4GBrickViewType::GROUPFORMCOPY) ||
            ($viewType == C4GBrickViewType::PROJECTPARENTFORMCOPY) ||
            ($viewType == C4GBrickViewType::PROJECTFORM) ||
            ($viewType == C4GBrickViewType::PROJECTFORMCOPY) ||
            ($viewType == C4GBrickViewType::MEMBERFORM) ||
            ($viewType == C4GBrickViewType::PUBLICFORM)) {
             return true;
        }

        return false;
    }
}
