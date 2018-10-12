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

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\CoreBundle\Resources\contao\classes\container\C4GContainer;
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
    public function getC4GDialogField($fieldList, C4GContainer $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
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