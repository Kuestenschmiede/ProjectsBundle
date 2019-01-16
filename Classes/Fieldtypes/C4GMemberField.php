<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2019
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\GroupsBundle\Resources\contao\models\MemberModel;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldText;

class C4GMemberField extends C4GBrickFieldText
{
    protected $size = 255;
    protected $maxLength = 255;

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
        // $value is an ID
        $member = MemberModel::findByPk($value);
        $groupId = $dialogParams->getGroupId();
        $value = MemberModel::getDisplaynameForGroup($groupId, $member->id);
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data,
                '<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata ' . $id . '" size="'.$this->size.'"  maxLength="'.$this->maxLength.'" name="' . $this->getFieldName() . '" value="' . $value . '">');
        }

        return $result;
    }

    public function getC4GPopupField($data, $groupId)
    {
        $value = $data[$this->getFieldName()];
        $member = MemberModel::findByPk($value);
        $value = MemberModel::getDisplaynameForGroup($groupId, $member->id);
        return "<p><b>". $this->getTitle() . "</b>: " . $value ."</p>";
    }


    /**
     * @param $dbValues
     * @param $dlgValues
     * @return array|C4GBrickFieldCompare|null
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }
        return $result;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param $size
     * @return $this|C4GBrickFieldText
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * @param $maxLength
     * @return $this|C4GBrickFieldText
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $maxLength;
        return $this;
    }


}