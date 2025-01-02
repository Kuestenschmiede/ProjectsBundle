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

class C4GAudioField extends C4GBrickField
{
    private $sound;

    /**
     * C4GAudioField constructor.
     */
    public function __construct($sound,string $type = C4GBrickFieldType::AUDIO)
    {
        $this->sound = $sound;
        parent::__construct($type);
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $result = '';

        $sound = $this->sound;
        if ($sound) {
            $condition = $this->createConditionData($fieldList, $data);

            $html =
                '<div id="' . $id . '" class="c4g__form-audio  c4gGuiDialogButtonsJqui">' .
                '<audio autoplay><source src="' . $sound . '" type="audio/mpeg">Your browser does not support the audio element.</audio>' .
                '</div>';

            $result = $this->addC4GField($condition, $dialogParams, $fieldList, $data, $html);
        }

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
    }

    /**
     * @return mixed
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * @param $sound
     * @return $this
     */
    public function setSound($sound)
    {
        $this->sound = $sound;

        return $this;
    }
}
