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
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

trait C4GTraitCheckMandatoryFields
{
    /**
     * @param $fieldList
     * @param $dlgValues
     * @param string $callback
     * @param string $callbackParams
     * @return array
     */
    public function checkMandatoryFields($fieldList, $dlgValues, $callback = 'focusOnElement', $callbackParams = '')
    {
        $mandatoryCheckResult = C4GBrickDialog::checkMandatoryFields($fieldList, $dlgValues);
        if ($mandatoryCheckResult !== true) {
            if ($mandatoryCheckResult instanceof C4GBrickField) {
                if ($mandatoryCheckResult->getSpecialMandatoryMessage() != '') {
                    return ['usermessage' => $mandatoryCheckResult->getSpecialMandatoryMessage(), 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                        'callback' => ['function' => 'focusOnElement', 'params' => 'c4g_' . $mandatoryCheckResult->getFieldName()], ];
                } elseif ($mandatoryCheckResult->getTitle() != '') {
                    if (!$callbackParams) {
                        $callbackParams = 'c4g_' . $mandatoryCheckResult->getFieldName();
                    }

                    return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_FIELD'] . '"' . $mandatoryCheckResult->getTitle() . '".',
                        'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE'],
                        'callback' => ['function' => $callback, 'params' => $callbackParams], ];
                }
            }

            return ['usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY'], 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['USERMESSAGE_MANDATORY_TITLE']];
        }

        $validate_result = C4GBrickDialog::validateFields($this->makeRegularFieldList($fieldList), $dlgValues);
        if ($validate_result) {
            return ['usermessage' => $validate_result, 'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['INVALID_INPUT']];
        }

        return [];
    }
}
