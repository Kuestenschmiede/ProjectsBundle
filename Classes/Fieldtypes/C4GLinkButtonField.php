<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 14.09.18
 * Time: 17:12
 */

namespace con4gis\ProjectsBundle\Classes\Fieldtypes;


use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GLinkButtonField extends C4GBrickField
{
    private $latitudeColumn = '';       //Database column that has the latitude value.
    private $longitudeColumn = '';      //Database column that has the longitude value
    private $zoom = 16;                 //Map zoom level
    private $targetPageId = 0;          //Id of the page that contains the target Map
    private $buttonLabel = '';
    private $newTab = false;            //true = the Link is opened in a new tab. false = the Link is opened in the same tab.

    public function __construct()
    {
        $this->setDatabaseField(false)
            ->setFormField(false)
            ->setTableColumn()
            ->setDatabaseField(false);
    }

    /**
     * @param C4GBrickField[] $fieldList
     * @param $data
     * @param C4GBrickDialogParams $dialogParams
     * @param array $additionalParams
     * @return array
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = array())
    {
        return array();
    }

    /**
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValue, $dlgvalue)
    {
        return array();
    }

    public function getC4GListField($rowData, $content)
    {
        $html = '';
        $latField = $this->latitudeColumn;
        $longField = $this->longitudeColumn;
        $lat = $rowData->$latField;
        $lon = $rowData->$longField;
        $zoom = $this->zoom;

        $class = 'ui-button ui-corner-all';
        if ($this->getStyleClass()) {
            $class .= $this->getStyleClass();
        }
        $href = \Contao\Controller::replaceInsertTags("{{link_url::".$this->targetPageId."}}");

        if ($this->newTab) {
            $rel = "target='_blank' rel='noopener noreferrer'";
        } else {
            $rel = '';
        }

        $html = "<a $rel href='$href' onclick='event.stopPropagation()'>";
        $html .= "<span class='$class'>";
        $html .= $this->getButtonLabel();
        $html .= "</span>";
        $html .= "</a>";


        return $html;
    }

    /**
     * @return string
     */
    public function getLatitudeColumn(): string
    {
        return $this->latitudeColumn;
    }

    /**
     * @param string $latitudeColumn
     * @return C4GLinkButtonField
     */
    public function setLatitudeColumn(string $latitudeColumn): C4GLinkButtonField
    {
        $this->latitudeColumn = $latitudeColumn;
        return $this;
    }

    /**
     * @return string
     */
    public function getLongitudeColumn(): string
    {
        return $this->longitudeColumn;
    }

    /**
     * @param string $longitudeColumn
     * @return C4GLinkButtonField
     */
    public function setLongitudeColumn(string $longitudeColumn): C4GLinkButtonField
    {
        $this->longitudeColumn = $longitudeColumn;
        return $this;
    }

    /**
     * @return int
     */
    public function getZoom(): int
    {
        return $this->zoom;
    }

    /**
     * @param int $zoom
     * @return C4GLinkButtonField
     */
    public function setZoom(int $zoom): C4GLinkButtonField
    {
        $this->zoom = $zoom;
        return $this;
    }

    /**
     * @return int
     */
    public function getTargetPageId(): int
    {
        return $this->targetPageId;
    }

    /**
     * @param int $targetPageId
     * @return C4GLinkButtonField
     */
    public function setTargetPageId(int $targetPageId): C4GLinkButtonField
    {
        $this->targetPageId = $targetPageId;
        return $this;
    }

    /**
     * @return string
     */
    public function getButtonLabel(): string
    {
        return $this->buttonLabel;
    }

    /**
     * @param string $buttonLabel
     * @return C4GLinkButtonField
     */
    public function setButtonLabel(string $buttonLabel): C4GLinkButtonField
    {
        $this->buttonLabel = $buttonLabel;
        return $this;
    }

}