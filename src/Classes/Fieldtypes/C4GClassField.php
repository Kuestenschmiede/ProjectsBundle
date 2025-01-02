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

class C4GClassField extends C4GBrickField
{
    public function __construct()
    {
        parent::__construct();
        $this->setTableColumn();
    }

    public function doesFieldValueMatch($fieldValue)
    {
        foreach ($this->getOptions() as $option) {
            if ($fieldValue === $option) {
                return true;
            }
        }

        return false;
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        return '';
    }

    public function compareWithDB($dbValues, $dlgValues)
    {
        return null;
    }
}
