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
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GPermalinkField extends C4GBrickField
{
    private $permaLinkName = '';

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::PERMALINK)
    {
        parent::__construct($type);
        $this->setDatabaseField(false);
        $this->setComparable(false);
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        if ($value && $this->getPermaLinkName()) {
            $permaLinkName = $this->getPermaLinkName();
            $value .= $data->$permaLinkName;
        }
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                //'<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata" name="' . $this->getFieldName() . '" value="' . $value . '">' .
                '<a class="c4g_dialog_link" href="' . $value . '" target="_blank" rel="noopener">' . $value . '</a>');
        }

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
    }

    /**
     * @return string
     */
    public function getPermaLinkName()
    {
        return $this->permaLinkName;
    }

    /**
     * @param $permaLinkName
     * @return $this
     */
    public function setPermaLinkName($permaLinkName)
    {
        $this->permaLinkName = $permaLinkName;

        return $this;
    }
}
