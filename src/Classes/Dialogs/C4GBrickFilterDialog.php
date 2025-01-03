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
namespace con4gis\ProjectsBundle\Classes\Dialogs;

use con4gis\CoreBundle\Classes\C4GHTMLFactory;
use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateTimePickerField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGeopickerField;
use con4gis\ProjectsBundle\Classes\Filter\C4GDateTimeListFilter;

class C4GBrickFilterDialog extends C4GBrickDialog
{
    protected $filter = null;

    /**
     * @param $memberId
     * @param $group_id
     * @return array
     */
    public function show()
    {
        $dialogParams = $this->getDialogParams();
        $fromFilterField = null;
        $fromCookie = null;
        $toFilterField = null;
        $toCookie = null;
        $confirmAction = C4GBrickActionType::ACTION_CONFIRMPARENTFILTER;
        $confirmButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['CONFIRM_BUTTON_TEXT'];
        $cancelAction = C4GBrickActionType::ACTION_CANCELPARENTFILTER;
        $cancelButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['CANCEL_BUTTON_TEXT'];
        $dialogId = -1;
        $brickKey = $dialogParams->getBrickKey();
        $filterParams = $dialogParams->getFilterParams();

        if ($this->filter instanceof C4GDateTimeListFilter) {
            $fromFilterField = new C4GDateField();
            $fromFilterField->setCustomFormat($GLOBALS['TL_CONFIG']['dateFormat']);
            $fromFilterField->setFieldName('fromFilter');
            $fromFilterField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['fromFilter']);
            $fromFilterField->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_fromFilter']);
            $fromFilterField->setTableColumn(false);
            $fromFilterField->setMandatory(true);
            $fromFilterField->setEditable(true);
            $fromFilterField->setIgnoreViewType(true);

            $toFilterField = new C4GDateField();
            $toFilterField->setCustomFormat($GLOBALS['TL_CONFIG']['dateFormat']);
            $toFilterField->setFieldName('toFilter');
            $toFilterField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['toFilter']);
            $toFilterField->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_toFilter']);
            $toFilterField->setTableColumn(false);
            $toFilterField->setMandatory(true);
            $toFilterField->setEditable(true);
            $toFilterField->setIgnoreViewType(true);

            $this->filter->getFilterCookies($brickKey);
            $from = $this->filter->getFrom();
            $to = $this->filter->getTo();

            if ($from) {
                $fromFilterField->setInitialValue($from);
            } else {
                $fromFilterField->setInitialValue(mktime(0, 0, 0, date('n'), 1));
            }

            if ($to) {
                $toFilterField->setInitialValue($to);
            } else {
                $toFilterField->setInitialValue(mktime(23, 59, 59, date('n'), date('t')));
            }
        }

        if ($filterParams && $filterParams->isWithGeoFilter()) {
            $filterParams->getBrickFilterCookies($brickKey);
            $firstPosition = new C4GGeopickerField();
            $firstPosition->setFieldName('firstPosition');
            $firstPosition->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['fromFilter']);
            $firstPosition->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_fromFilter']);
//            $firstPosition->setType(C4GBrickFieldType::GEOPICKER);
            $firstPosition->setTableColumn(false);
            $firstPosition->setMandatory(true);
            $firstPosition->setEditable(true);

            $fieldList[] = $firstPosition;
        }

        if ($filterParams && $filterParams->isWithRangeFilter()) {
            $filterParams->getBrickFilterCookies($brickKey);

            if ($filterParams->getRangeFrom()) {
                $rangeFrom = C4GBrickCommon::getTimestampFromDate($filterParams->getRangeFrom());
            }

            if ($filterParams->getRangeTo()) {
                $rangeTo = C4GBrickCommon::getTimestampFromDate($filterParams->getRangeTo());
            }

            if ($filterParams->isDateTimeFilter()) {
                $fromFilterField = new C4GDateTimePickerField();
                $fromFilterField->setCustomFormat($GLOBALS['TL_CONFIG']['datimFormat']);
            } else {
                $fromFilterField = new C4GDateField();
                $fromFilterField->setCustomFormat($GLOBALS['TL_CONFIG']['dateFormat']);
            }
            $fromFilterField->setFieldName('fromFilter');
            $fromFilterField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['fromFilter']);
            $fromFilterField->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_fromFilter']);
            $fromFilterField->setTableColumn(false);
            $fromFilterField->setMandatory(true);
            $fromFilterField->setEditable(true);
            $fromFilterField->setIgnoreViewType(true);

            if ($rangeFrom) {
                $fromFilterField->setInitialValue($rangeFrom);
            } else {
                $fromFilterField->setInitialValue(time() - 4 * 604800);
            }

            $fieldList[] = $fromFilterField;

            if ($filterParams->isDateTimeFilter()) {
                $toFilterField = new C4GDateTimePickerField();
                $toFilterField->setCustomFormat($GLOBALS['TL_CONFIG']['datimFormat']);
            } else {
                $toFilterField = new C4GDateField();
                $toFilterField->setCustomFormat($GLOBALS['TL_CONFIG']['dateFormat']);
            }
            $toFilterField->setFieldName('toFilter');
            $toFilterField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['toFilter']);
            $toFilterField->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_toFilter']);
            $toFilterField->setTableColumn(false);
            $toFilterField->setMandatory(true);
            $toFilterField->setEditable(true);
            $toFilterField->setIgnoreViewType(true);

            if ($rangeTo) {
                $toFilterField->setInitialValue($rangeTo);
            } else {
                $toFilterField->setInitialValue(time());
            }

            $fieldList[] = $toFilterField;
        }

        if ($fromFilterField && $toFilterField) {
            //ToDo verschiedene Filter unterstützen

            $view = '<div class="' . C4GBrickConst::CLASS_DIALOG . ' ' . C4GBrickConst::CLASS_FILTER_DIALOG . ' c4g__content">';
            $view .= C4GHTMLFactory::lineBreak() . $fromFilterField->getC4GDialogField(null, null, $dialogParams);
            $view .= C4GHTMLFactory::lineBreak() . $toFilterField->getC4GDialogField(null, null, $dialogParams);

            $messageTitle = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINE_TEXT'];

            return [
                'dialogtype' => 'html',
                'dialogdata' => $view,
                'dialogoptions' => C4GUtils::addDefaultDialogOptions([
                    'title' => $messageTitle,
                    'modal' => true,
                ]),
                'dialogid' => C4GBrickActionType::IDENTIFIER_FILTER . $dialogId,
                'dialogstate' => C4GBrickActionType::IDENTIFIER_FILTER . ':' . $dialogId,
                'dialogbuttons' => [
                    [
                        'action' => $confirmAction . ':' . $dialogId,
                        'class' => 'c4gGuiDefaultAction',
                        'type' => 'send',
                        'text' => $confirmButtonText,
                    ],
                    [
                        'action' => $cancelAction . ':' . $dialogId,
                        'class' => 'c4gGuiDefaultAction',
                        'type' => 'send',
                        'text' => $cancelButtonText,
                    ],
                ],
            ];
        } elseif ($filterParams->isWithGeoFilter()) {
            $content = $firstPosition->getContentId();
            $view = '<div class="' . C4GBrickConst::CLASS_DIALOG . ' ' . C4GBrickConst::CLASS_FILTER_DIALOG . ' c4g__content">';
            $view .= C4GHTMLFactory::lineBreak() . $firstPosition->getC4GDialogField(null, null, $dialogParams, ['content' => $content]);

            $messageTitle = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINE_TEXT'];

            return [
                'dialogtype' => 'html',
                'dialogdata' => $view,
                'dialogoptions' => C4GUtils::addDefaultDialogOptions([
                    'title' => $messageTitle,
                    'modal' => true,
                ]),
                'dialogid' => C4GBrickActionType::IDENTIFIER_FILTER . $dialogId,
                'dialogstate' => C4GBrickActionType::IDENTIFIER_FILTER . ':' . $dialogId,
                'dialogbuttons' => [
                    [
                        'action' => $confirmAction . ':' . $dialogId,
                        'class' => 'c4gGuiDefaultAction',
                        'type' => 'send',
                        'text' => $confirmButtonText,
                    ],
                    [
                        'action' => $cancelAction . ':' . $dialogId,
                        'class' => 'c4gGuiDefaultAction',
                        'type' => 'send',
                        'text' => $cancelButtonText,
                    ],
                ],
            ];
        }
    }

    /**
     * @param null $filter
     * @return C4GBrickFilterDialog
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }
}
