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

use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainerContainer;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialog;

class C4GShowMessageChangesDialogAction extends C4GBrickDialogAction
{
    /**
     * @var C4GContainerContainer
     */
    private $diffs = null;

    public function run()
    {

        $fieldListObject = $this->module->getFieldListObject();
        $changesHtml = '';
        foreach ($this->diffs as $index => $diff) {
            if ($index === 'uuid') {
                continue;
            }
            $field = $fieldListObject->getByKey($index);
            if ($field === null) {
                continue;
            }
            $title = $field->getTitle();
            $oldValue = $field->translateFieldValue($diff->getByKey('dbValue'));
            $newValue = $field->translateFieldValue($diff->getByKey('dialogValue'));
            $changesHtml .= "<li>$title ($oldValue => $newValue)</li>";
        }


        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $id = $dialogParams->getId();

        if ($changesHtml === '') {
            $dialogParams->setId(-1);
            $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            $action->setModule($this->module);
            return $action->run();
        } else {
            return C4GBrickDialog::showC4GMessageDialog(
                $id,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_QUESTION'],
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_FIELDLIST'].'</br>'."<ul>$changesHtml</ul>",
                C4GBrickActionType::ACTION_CONFIRMMESSAGE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_YES'],
                C4GBrickActionType::ACTION_CANCELMESSAGE,
                $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['MESSAGE_DIALOG_CLOSE_DIALOG_NO'],
                $dlgValues);
        }
    }

    /**
     * @return null
     * @deprecated
     */
    public function getChanges()
    {
        return '';
    }

    /**
     * @param $changes
     * @return $this
     * @deprecated
     */
    public function setChanges($changes)
    {
        return $this;
    }

    /**
     * @return null
     */
    public function getDiffs()
    {
        return $this->diffs;
    }

    /**
     * @param $diffs
     * @return $this
     */
    public function setDiffs($diffs)
    {
        $this->diffs = $diffs;
        return $this;
    }



    public function isReadOnly()
    {
        return true;
    }
}
