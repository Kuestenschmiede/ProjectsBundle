<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GIconField extends C4GBrickField
{
    protected $icon = '';
    protected $iconText = '';
    protected $conditional = false;     //true = the icon is only shown if the field value is '1'

    public function __construct()
    {
        parent::__construct();
        $this->setDatabaseField(false)
            ->setFormField(false)
            ->setTableColumn();
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        if ($this->iconText) {
            $text = '<span>' . $this->iconText . '</span>';
        } else {
            $text = '';
        }

        return '<div class="' . $this->getStyleClass() . '">' . $this->getC4GListField($data, '') . $text . '</div>';
    }

    /**
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
        return [];
    }

    final public function getC4GListField($rowData, $content)
    {
        $fieldName = $this->getFieldName();
        if (!$this->conditional || ($rowData->$fieldName === '1')) {
            return '<span title="' . $this->getTitle() . '">' . $this->icon . '</span>';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return C4GIconField
     */
    public function setIcon(string $icon): C4GIconField
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getIconText(): string
    {
        return $this->iconText;
    }

    /**
     * @param string $iconText
     * @return C4GIconField
     */
    public function setIconText(string $iconText): C4GIconField
    {
        $this->iconText = $iconText;

        return $this;
    }

    /**
     * @return bool
     */
    public function isConditional(): bool
    {
        return $this->conditional;
    }

    /**
     * @param bool $conditional
     * @return C4GIconField
     */
    public function setConditional(bool $conditional = true): C4GIconField
    {
        $this->conditional = $conditional;

        return $this;
    }
}
