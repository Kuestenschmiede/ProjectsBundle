<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

class C4GShowMessageChangesDialogAction extends C4GBrickDialogAction
{
    private $changes = null;

    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $id = $dialogParams->getId();

        $brickDatabase = $this->getBrickDatabase();
        if ($brickDatabase && ($id > 0)) {
            $dbValues = $this->getBrickDatabase()->findByPk($id);
        }

        if ($this->changes) {
            $changes = $this->changes;
        } else {
            $changes = C4GBrickDialog::compareWithDB($this->makeRegularFieldList($this->getFieldList(), $dlgValues, $dbValues, $this->viewType, $dialogParams->isFrozen()));
        }

        if ($changes) {
            //$fields = array();
            $fields = '<ul>';
            $message_cnt = 0;
            foreach ($changes as $changedField) {
                $field = $changedField->getField();
                if ($field->getFieldName() == 'uuid') {
                    continue;
                }
                $fieldTitle = $field->getTitle();
                $value1 = false;
                $value2 = false;

                if ($field && $changedField->getDbValue()) {
                    $value1 = $field->translateFieldValue($changedField->getDbValue());
                }

                if ($field && $changedField->getDlgValue()) {
                    $value2 = $field->translateFieldValue($changedField->getDlgValue());
                }

                if (!$value1) {
                    $value1 = $GLOBALS['TL_LANG']['tl_c4g_projects']['noentry'];
                }

                if (!$value2) {
                    $value2 = $GLOBALS['TL_LANG']['tl_c4g_projects']['noentry'];
                }

                if (($value1 || $value2) && ($value1 != $value2)) {
                    $fields .= '<li>' . $fieldTitle . ' (' . $value1 . ' => ' . $value2 . ')</li>';
                    $message_cnt++;
                }
            }
            $fields .= '</ul>';

            if ($message_cnt == 0 || $dialogParams->isHideChangesMessage()) {
                $dialogParams->setId(-1);
                $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());

                return $action->run();
            }
            $result = C4GBrickDialog::showC4GMessageDialog(
                    $id,
                    $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_QUESTION'],
                    $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_FIELDLIST'] . '</br>' . $fields,
                    C4GBrickActionType::ACTION_CONFIRMMESSAGE,
                    $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_YES'],
                    C4GBrickActionType::ACTION_CANCELMESSAGE,
                    $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_NO'],
                    $dlgValues);
        } else {
            $result = C4GBrickDialog::showC4GMessageDialog(
                $id,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_QUESTION'],
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_TEXT'],
                C4GBrickActionType::ACTION_CONFIRMMESSAGE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_YES'],
                C4GBrickActionType::ACTION_CANCELMESSAGE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_NO'],
                $dlgValues);
        }

        return $result;
    }

    /**
     * @return null
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * @param $changes
     * @return $this
     */
    public function setChanges($changes)
    {
        $this->changes = $changes;

        return $this;
    }

    public function isReadOnly()
    {
        return true;
    }
}
