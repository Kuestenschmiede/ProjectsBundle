<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 18.07.18
 * Time: 15:27
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\GroupsBundle\Resources\contao\models\MemberModel;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;

class C4GLastChangeField extends C4GMemberField
{
    private $withcolon = false;
    private $titleText = "";

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        // $value is an ID
        $member = MemberModel::findByPk($value);
        $groupId = $dialogParams->getGroupId();
        $value = MemberModel::getDisplaynameForGroup($groupId, $member->id);
        if ($data->tstamp) {
            $resVal = sprintf("Zuletzt geändert am %s durch %s", date('d.m.Y H:i:s', $data->tstamp), $value);
        } else {
            $resVal = $value;
        }

        $result = '';

        if ($this->isShowIfEmpty() || !empty($resVal)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata ' . $id . '" size="'.$this->size.'"  maxLength="'.$this->maxLength.'" name="' . $this->getFieldName() . '" value="' . $resVal . '">');
        }

        return $result;
    }

    public function getC4GPopupField($data, $groupId)
    {
        $value = $data[$this->getFieldName()];
        $member = MemberModel::findByPk($value);
        $value = MemberModel::getDisplaynameForGroup($groupId, $member->id);
        if ($data['tstamp']) {
            $result = sprintf("<b>Zuletzt geändert am</b> %s durch %s", date('d.m.Y H:i:s', $data['tstamp']), $value);
        } else {
            $result = $value;
        }
        $colon = " ";
        if ($this->withcolon) {
            $colon = ": ";
        }
        return "<p><b>". $this->titleText . "</b>" . $colon . $result ."</p>";
    }

    /**
     * @return bool
     */
    public function isWithcolon()
    {
        return $this->withcolon;
    }

    /**
     * @param bool $withcolon
     */
    public function setWithcolon($withcolon)
    {
        $this->withcolon = $withcolon;
    }

    /**
     * @return string
     */
    public function getTitleText()
    {
        return $this->titleText;
    }

    /**
     * @param string $titleText
     */
    public function setTitleText($titleText)
    {
        $this->titleText = $titleText;
    }
}