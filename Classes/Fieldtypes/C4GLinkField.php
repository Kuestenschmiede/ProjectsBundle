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

class C4GLinkField extends C4GBrickField
{
    /**
     * @return mixed
     */
    public function getLinkLabel()
    {
        return $this->linkLabel;
    }

    /**
     * @param mixed $linkLabel
     */
    public function setLinkLabel($linkLabel)
    {
        $this->linkLabel = $linkLabel;
    }


    /**
     * C4GLinkField constructor.
     */
    public function __construct()
    {
        $this->setDatabaseField(false);
        $this->setComparable(false);
        $this->linkLabel = $this->getLinkLabel();
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
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {

            $condition = $this->createConditionData($fieldList, $data);

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<a ' . $required . ' ' . $condition['conditionPrepare'] . ' id="' . $id . '" class="formdata ' . $id . ' c4g_brick_link" href="' . $value . '">' .$this->getLinkLabel() . '</a>');
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
}