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

use con4gis\ProjectsBundle\Classes\Buttons\C4GMoreButton;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Lists\C4GBrickRenderMode;

class C4GMoreButtonField extends C4GBrickField
{
    // override parent fields
    protected $databaseField = false;

    /**
     * The C4GMoreButton object that belongs to this field.
     * @var C4GMoreButton
     */
    protected $moreButton = null;

    /**
     * Classname for the button.
     * @var string
     */
    protected $className = 'c4g_brick_more_button';

    /**
     * Caption for the button.
     * @var string
     */
    protected $buttonTitle = '...';

    /**
     * C4GMoreButtonField constructor.
     */
    public function __construct()
    {
        $this->setSortColumn(false);
        $this->setSort(false);
        $this->setTableColumnPriority(1);
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return C4GBrickFieldCompare
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
        // should be called never
        return new C4GBrickFieldCompare($this, '', '');
    }

    /**
     * Public method for creating the field specific dialog HTML
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param $dialogParams
     * @param array $additionalParams
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $tableo = '';
        $tablec = '';
        $tdo = '';
        $tdc = '';
        $tro = '';
        $trc = '';

        $class = '';
        if ($this->getStyleClass()) {
            $class = 'class=' . $this->getStyleClass() . ' ';
        }

        $fieldData = $this->moreButton->renderButton(
            $this->className,
            $this->buttonTitle,
            $this->getFieldName(),
            'dialog',
            $dialogParams->getId()
        );

        if (($dialogParams && $dialogParams->isTableRows()) || $this->isTableRow()) {
            //$linebreak = '';
            $tableo = '<table class="c4g_brick_table_rows" style="width:' . $this->getTableRowWidth() . '">';
            $tro = '<tr>';
            $trc = '</tr>';
            $tdo = '<td>';
            $tdc = '</td>';
            $tablec = '</table>';
        }
        $id = 'c4g_condition';
        if ($dialogParams->getId() != -1) {
            $id .= '_' . $dialogParams->getId();
        }

        return '<div id=' . $id . ' '
            . $class
            . '>' .
            $tableo . $tro .
            $tdo . $fieldData . $tdc . $trc . $tablec .
             '</div>';
    }

    public function getC4GListField($rowData, $content)
    {
        if ($this->moreButton) {
            $value = $this->moreButton->renderButton(
                $this->className,
                $this->buttonTitle,
                $this->getFieldName(),
                C4GBrickRenderMode::TABLEBASED,
                $rowData->id
            );
        } else {
            $value = '';
        }
        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue() . $value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value . $this->getAddStrBehindValue();
        }

        return $value;
    }

    public function getC4GTileField($fieldTitle, $element)
    {
        if ($this->moreButton) {
            return $this->moreButton->renderButton(
                $this->className,
                $this->buttonTitle,
                $this->getFieldName(),
                C4GBrickRenderMode::TILEBASED,
                $element->id
            );
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isDatabaseField()
    {
        return $this->databaseField;
    }

    /**
     * @param bool $databaseField
     * @return $this|C4GBrickField
     */
    public function setDatabaseField($databaseField)
    {
        $this->databaseField = $databaseField;

        return $this;
    }

    /**
     * @return C4GMoreButton
     */
    public function getMoreButton()
    {
        return $this->moreButton;
    }

    /**
     * @param $moreButton
     * @return $this
     */
    public function setMoreButton($moreButton)
    {
        $this->moreButton = $moreButton;

        return $this;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param $className
     * @return $this
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->buttonTitle;
    }

    /**
     * @param $buttonTitle
     * @return $this
     */
    public function setButtonTitle($buttonTitle)
    {
        $this->buttonTitle = $buttonTitle;

        return $this;
    }
}
