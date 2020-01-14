<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GClassField extends C4GBrickField
{
    public function doesFieldValueMatch($fieldValue) {
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
