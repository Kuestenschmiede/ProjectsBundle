<?php
/**
 * Created by PhpStorm.
 * User: mei
 * Date: 08.03.17
 * Time: 08:30
 */

namespace con4gis\ProjectBundle\Classes\Dialogs;


use con4gis\ProjectBundle\Classes\Actions\C4GBrickActionType;
use con4gis\ProjectBundle\Classes\Common\C4GBrickCommon;
use con4gis\ProjectBundle\Classes\Fieldtypes\C4GSelectField;

class C4GBrickSelectGroupDialog extends C4GBrickDialog
{

    /**
     * @param $memberId
     * @param $group_id
     * @return array
     */
    public function show()
    {
        $dialogParams = $this->getDialogParams();
        $memberId = $dialogParams->getMemberId();
        $group_id = $dialogParams->getGroupId();
        $brickKey = $dialogParams->getBrickKey();
        $pluralCaption = $dialogParams->getBrickCaptionPlural();
        $confirmAction = C4GBrickActionType::ACTION_CONFIRMGROUPSELECT;
        $confirmButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_DIALOG_CONFIRM_BUTTON'];

        $cancelAction = C4GBrickActionType::ACTION_CANCELGROUPSELECT;
        $cancelButtonText = $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_DIALOG_CANCEL_BUTTON'];

        $grouplist = array();
        $groups = null;
        if ($GLOBALS['con4gis_groups_extension']['installed']) {
            $groups = C4GBrickCommon::getGroupListForBrick($memberId, $brickKey);
        }

        if (!$groups) {
            return array(
                'usermessage' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_DIALOG_PERMISSION_DENIED'],
                'title' => $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_DIALOG_PERMISSION_DENIED_TITLE']
            );
        }

        foreach($groups as $group) {
            $grouplist[] = array(
                'id'     => $group->id,
                'name'   => $group->name);
        }

        $groupField = new C4GSelectField();
        if ($dialogParams->getGroupKeyField()) {
            $groupFieldName = $dialogParams->getGroupKeyField();
        } else {
            $groupFieldName = 'group_id';
        }
        $groupField->setFieldName($groupFieldName);
        $groupField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP']);
        $groupField->setTitle($GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_TEXT'].$pluralCaption.$GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_TEXT_2']);
        $groupField->setSortColumn(false);
        $groupField->setTableColumn(false);
        $groupField->setSize(1);
        $groupField->setOptions($grouplist);
        $groupField->setChosen(true);

        if ( $group_id && ($group_id > -1)) {
            $groupField->setInitialValue($group_id);
        }

        return C4GBrickDialog::showC4GSelectDialog($dialogParams, $groupField,
            $GLOBALS['TL_LANG']['FE_C4G_DIALOG']['SELECT_GROUP_DIALOG_CONFIRM_QUESTION'],
            $confirmAction, $confirmButtonText, $cancelAction, $cancelButtonText);
    }

}