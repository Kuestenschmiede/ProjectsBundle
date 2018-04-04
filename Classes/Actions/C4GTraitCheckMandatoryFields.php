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


use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

trait C4GTraitCheckMandatoryFields
{
    /**
     * @param $fieldList
     * @param $dlgValues
     * @return array
     */
    function checkMandatoryFields($fieldList, $dlgValues)
    {
        $mandatoryCheckResult = C4GBrickDialog::checkMandatoryFields($fieldList, $dlgValues);
        if ($mandatoryCheckResult !== true) {
            if ($mandatoryCheckResult instanceof C4GBrickField) {
                if ($mandatoryCheckResult->getSpecialMandatoryMessage() != '') {
                    return array('usermessage' => $mandatoryCheckResult->getSpecialMandatoryMessage(), 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                        'callback' => array('function' => 'focusOnElement', 'params' => 'c4g_' . $mandatoryCheckResult->getFieldName()));
                } elseif ($mandatoryCheckResult->getTitle() != '') {
                    return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_FIELD'].'"'. $mandatoryCheckResult->getTitle().'".',
                        'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                        'callback' => array('function' => 'focusOnElement', 'params' => 'c4g_'. $mandatoryCheckResult->getFieldName()));
                }
            }
            return array('usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE']);
        }

        $validate_result = C4GBrickDialog::validateFields($this->makeRegularFieldList($fieldList), $dlgValues);
        if ($validate_result) {
            return array('usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['INVALID_INPUT']);
        }
        return array();
    }
}