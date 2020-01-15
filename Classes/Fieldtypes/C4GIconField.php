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

use con4gis\CoreBundle\Resources\contao\models\C4gLogModel;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GIconField extends C4GBrickField
{
    protected $icon = '';
    protected $conditional = false;     //true = the icon is only shown if the field value is '1'

    public function __construct()
    {
        parent::__construct();
        $this->setDatabaseField(false)
            ->setFormField(false)
            ->setTableColumn();
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return array
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        return [];
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
            $class = 'ui-button ui-corner-all c4g_icon';
            if ($this->getStyleClass()) {
                $class .= $this->getStyleClass();
            }

            $html = "<span class=\"$class\" title=\"".$this->getDescription()."\">";
            $html .= $this->icon;
            $html .= '</span>';

            return $html;
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
