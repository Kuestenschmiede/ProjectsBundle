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

class C4GDataClassField extends C4GBrickField
{
    protected $classPrefix = '';
    protected $classSuffix = '';
    protected $splitBy = ', ';

    public function __construct()
    {
        parent::__construct();
        $this->setTableColumn();
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        return '';
    }

    public function compareWithDB($dbValues, $dlgValues)
    {
        return null;
    }

    public function getClass(string $class)
    {
        return $this->classPrefix . $class . $this->classSuffix;
    }

    /**
     * @param string $classPrefix
     * @return C4GDataClassField
     */
    public function setClassPrefix(string $classPrefix): C4GDataClassField
    {
        $this->classPrefix = $classPrefix;

        return $this;
    }

    /**
     * @param string $classSuffix
     * @return C4GDataClassField
     */
    public function setClassSuffix(string $classSuffix): C4GDataClassField
    {
        $this->classSuffix = $classSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getSplitBy(): string
    {
        return $this->splitBy;
    }

    /**
     * @param string $splitBy
     * @return C4GDataClassField
     */
    public function setSplitBy(string $splitBy): C4GDataClassField
    {
        $this->splitBy = $splitBy;
        return $this;
    }
}
