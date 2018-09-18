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


use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GLinkButtonField extends C4GBrickField
{
    protected $targetMode = self::TARGET_MODE_PAGE;
    protected $targetPageId = 0;        //target page ID
    protected $targetPageUrl = '';      //target URL
    protected $buttonLabel = '';
    protected $newTab = false;          //true = the Link is opened in a new tab. false = the Link is opened in the same tab.

    const TARGET_MODE_PAGE = 'page';    //Links to an internal page with a given page ID
    const TARGET_MODE_URL = 'url';      //Links to a page or route with a given url string

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

    public final function getC4GListField($rowData, $content)
    {
        $class = 'ui-button ui-corner-all';
        if ($this->getStyleClass()) {
            $class .= $this->getStyleClass();
        }

        if ($this->newTab) {
            $rel = "target='_blank' rel='noopener noreferrer'";
        } else {
            $rel = '';
        }

        $href = $this->createHref($rowData, $content);
        $title = $this->getTitle();

        $html = "<a $rel href='$href' title='$title' onclick='event.stopPropagation()'>";
        $html .= "<span class='$class'>";
        $html .= $this->buttonLabel;
        $html .= "</span>";
        $html .= "</a>";


        return $html;
    }

    protected function createHref($rowData, $content) {
        if ($this->targetMode === self::TARGET_MODE_PAGE) {
            $href = \Contao\Controller::replaceInsertTags("{{link_url::".$this->targetPageId."}}");
        } elseif ($this->targetMode === self::TARGET_MODE_URL) {
            $href = $this->targetPageUrl;
        } else {
            return '';
        }
        return $href;
    }

    /**
     * @return string
     */
    public function getTargetMode(): string
    {
        return $this->targetMode;
    }

    /**
     * @param string $targetMode
     * @return $this
     */
    public function setTargetMode(string $targetMode)
    {
        $this->targetMode = $targetMode;
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
     * @return int
     */
    public function getTargetPageUrl(): int
    {
        return $this->targetPageUrl;
    }

    /**
     * @param string $targetPageUrl
     * @return $this
     */
    public function setTargetPageUrl(string $targetPageUrl)
    {
        $this->targetPageUrl = $targetPageUrl;
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

    /**
     * @return bool
     */
    public function isNewTab(): bool
    {
        return $this->newTab;
    }

    /**
     * @param bool $newTab
     * @return C4GLinkButtonField
     */
    public function setNewTab(bool $newTab = true): C4GLinkButtonField
    {
        $this->newTab = $newTab;
        return $this;
    }



}