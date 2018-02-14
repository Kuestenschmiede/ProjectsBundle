<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Buttons\C4GMoreButton;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;

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
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
//        $condition = $this->createConditionData($fieldList, $data);
//        $button = $this->moreButton ?
//            $this->moreButton->renderButton($this->className, $this->buttonTitle, $this->getFieldName()) : "";
//        $result =
//            $this->addC4GField($condition,$dialogParams,$fieldList,$data, $button);
//        return $result;
        $tableo = '';
        $tablec = '';
        $tdo = '';
        $tdc = '';
        $tro = '';
        $trc = '';

        $class = '';
        if ($this->getStyleClass()) {
            $class = 'class='.$this->getStyleClass().' ';
        }

        $fieldData = $this->moreButton->renderButton($this->className, $this->buttonTitle, $this->getFieldName());

        if (($dialogParams && $dialogParams->isTableRows()) || $this->isTableRow()) {
            //$linebreak = '';
            $tableo = '<table class="c4g_brick_table_rows" style="width:'.$this->getTableRowWidth().'">';
            $tro = '<tr>';
            $trc = '</tr>';
            $tdo = '<td>';
            $tdc = '</td>';
            $tablec = '</table>';
        }
        $id = "c4g_condition";
        if ($dialogParams->getId() != -1) {
            $id .= '_' . $dialogParams->getId();
        }

        return '<div id='. $id .' '
            . $class
            . '>' .
            $tableo.$tro.
            $tdo.$fieldData.$tdc.$trc.$tablec.
             '</div>';
    }

    public function getC4GListField($rowData, $content)
    {
        $value = $this->moreButton ?
            $this->moreButton->renderButton($this->className, $this->buttonTitle, $this->getFieldName()) : "";
        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue().$value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value.$this->getAddStrBehindValue();
        }
        return $value;
    }

    public function getC4GTileField($fieldTitle, $element)
    {
        return $this->moreButton ?
            $this->moreButton->renderButton($this->className, $this->buttonTitle, $this->getFieldName()) : "";
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
     */
    public function setDatabaseField($databaseField)
    {
        $this->databaseField = $databaseField;
    }

    /**
     * @return C4GMoreButton
     */
    public function getMoreButton()
    {
        return $this->moreButton;
    }

    /**
     * @param C4GMoreButton $moreButton
     */
    public function setMoreButton($moreButton)
    {
        $this->moreButton = $moreButton;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function getButtonTitle(): string
    {
        return $this->buttonTitle;
    }

    /**
     * @param string $buttonTitle
     */
    public function setButtonTitle(string $buttonTitle)
    {
        $this->buttonTitle = $buttonTitle;
    }
}