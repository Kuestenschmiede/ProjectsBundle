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
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GPermalinkField extends C4GBrickField
{
    private $permaLinkName = '';

    /**
     * C4GPermalinkField constructor.
     * @param string $permaLinkName
     */
    public function __construct()
    {
        $this->setDatabaseField(false);
        $this->setComparable(false);
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        $id = "c4g_" . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams);
        $value = $this->generateInitialValue($data);
            if($value && $this->getPermaLinkName()) {
                $permaLinkName = $this->getPermaLinkName();
                $value .= $data->$permaLinkName;
            }
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                //'<input ' . $required . ' ' . $condition['conditionPrepare'] . ' type="text" id="' . $id . '" class="formdata" name="' . $this->getFieldName() . '" value="' . $value . '">' .
                '<a class="c4g_dialog_link" href="'.$value.'" target="_blank" rel="noopener">'.$value.'</a>');
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