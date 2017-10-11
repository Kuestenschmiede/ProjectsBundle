<?php
/**
 * Created by PhpStorm.
 * User: mei
 * Date: 08.03.17
 * Time: 08:30
 */

namespace con4gis\ProjectsBundle\Classes\Dialogs;


use con4gis\CoreBundle\Resources\contao\classes\C4GHTMLFactory;
use con4gis\CoreBundle\Resources\contao\classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GDateField;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GGeopickerField;

class C4GBrickFilterDialog extends C4GBrickDialog
{

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
        $dialogId = $dialogParams->getMemberId();
        $brickKey = $dialogParams->getBrickKey();
        $filterParams = $dialogParams->getFilterParams();

        if($filterParams && $filterParams->isWithGeoFilter()) {

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

        if($filterParams && $filterParams->isWithRangeFilter()) {

            $filterParams->getBrickFilterCookies($brickKey);

            if ($filterParams->getRangeFrom()) {
                $rangeFrom = strtotime($filterParams->getRangeFrom());
            }

            if ($filterParams->getRangeTo()) {
                $rangeTo = strtotime($filterParams->getRangeTo());
            }

            $fromFilterField = new C4GDateField();
            $fromFilterField->setFieldName('fromFilter');
            $fromFilterField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['fromFilter']);
            $fromFilterField->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_fromFilter']);
            $fromFilterField->setTableColumn(false);
            $fromFilterField->setMandatory(true);
            $fromFilterField->setEditable(true);

            if($rangeFrom) {
                $fromFilterField->setInitialValue($rangeFrom);
            } else {
                $fromFilterField->setInitialValue(time() - 4 * 604800);
            }

            $fieldList[] = $fromFilterField;

            $toFilterField = new C4GDateField();
            $toFilterField->setFieldName('toFilter');
            $toFilterField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['toFilter']);
            $toFilterField->setDescription($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['desc_toFilter']);
            $toFilterField->setTableColumn(false);
            $toFilterField->setMandatory(true);
            $toFilterField->setEditable(true);

            if($rangeTo) {
                $toFilterField->setInitialValue($rangeTo);
            } else {
                $toFilterField->setInitialValue(time());
            }

            $fieldList[] = $toFilterField;
        }

        if ($fromFilterField && $toFilterField) {
            //ToDo verschiedene Filter unterstützen

            $view = '<div class="' . C4GBrickConst::CLASS_DIALOG . ' ' . C4GBrickConst::CLASS_FILTER_DIALOG . ' ui-widget ui-widget-content ui-corner-bottom">';
            $view .= C4GHTMLFactory::lineBreak() . $fromFilterField->getC4GDialogField(null, null, $dialogParams);
            $view .= C4GHTMLFactory::lineBreak() . $toFilterField->getC4GDialogField(null, null, $dialogParams);

            $messageTitle = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINE_TEXT'];

            return array
            (
                'dialogtype' => 'html',
                'dialogdata' => $view,
                'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
                (
                    'title' => $messageTitle,
                    'modal' => true
                )),
                'dialogid' => C4GBrickActionType::IDENTIFIER_FILTER.$dialogId,
                'dialogstate' => C4GBrickActionType::IDENTIFIER_FILTER.':'.$dialogId,
                'dialogbuttons' => array
                (
                    array
                    (
                        'action' => $confirmAction.':'.$dialogId,
                        'class'  => 'c4gGuiDefaultAction',
                        'type'   => 'send',
                        'text'   => $confirmButtonText,
                    ),
                    array
                    (
                        'action' => $cancelAction.':'.$dialogId,
                        'class'  => 'c4gGuiDefaultAction',
                        'type'   => 'send',
                        'text'   => $cancelButtonText,
                    ),
                )
            );
        } else if($filterParams->isWithGeoFilter()) {
            $content = $firstPosition->getContentId();
            $view =  '<div class="' . C4GBrickConst::CLASS_DIALOG . ' ' . C4GBrickConst::CLASS_FILTER_DIALOG . ' ui-widget ui-widget-content ui-corner-bottom">';
            $view .= C4GHTMLFactory::lineBreak() . $firstPosition->getC4GDialogField(null, null, $dialogParams, array('content' => $content));

            $messageTitle = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['HEADLINE_TEXT'];

            return array
            (
                'dialogtype' => 'html',
                'dialogdata' => $view,
                'dialogoptions' => C4GUtils::addDefaultDialogOptions(array
                (
                    'title' => $messageTitle,
                    'modal' => true
                )),
                'dialogid' => C4GBrickActionType::IDENTIFIER_FILTER.$dialogId,
                'dialogstate' => C4GBrickActionType::IDENTIFIER_FILTER.':'.$dialogId,
                'dialogbuttons' => array
                (
                    array
                    (
                        'action' => $confirmAction.':'.$dialogId,
                        'class'  => 'c4gGuiDefaultAction',
                        'type'   => 'send',
                        'text'   => $confirmButtonText,
                    ),
                    array
                    (
                        'action' => $cancelAction.':'.$dialogId,
                        'class'  => 'c4gGuiDefaultAction',
                        'type'   => 'send',
                        'text'   => $cancelButtonText,
                    ),
                )
            );
        }
    }

}