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

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;

class C4GLinkButtonField extends C4GBrickField
{
    protected $targetMode = self::TARGET_MODE_PAGE;
    protected $targetPageId = 0;        //target page ID
    protected $targetPageUrl = '';      //target URL
    protected $buttonLabel = '';
    protected $newTab = false;          //true = the Link is opened in a new tab. false = the Link is opened in the same tab.
    protected $conditional = false;     //true = the Link is only shown if the field value is '1'

    const TARGET_MODE_PAGE = 'page';    //Links to an internal page with a given page ID
    const TARGET_MODE_URL = 'url';      //Links to a page or route with a given url string

    public function __construct()
    {
        parent::__construct();
        $this->setDatabaseField(false)
            ->setFormField(false)
            ->setTableColumn();
    }

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        return $this->getC4GListField($data, '');
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

            $label = $this->buttonLabel ?: $href;

            $html = "<a $rel href='$href' title='$title' onclick='event.stopPropagation()'>";
            $html .= "<span class='$class'>";
            $html .= $label;
            $html .= '</span>';
            $html .= '</a>';

            return $html;
        }

        return '';
    }

    protected function createHref($rowData, $content)
    {
        if ($this->targetMode === self::TARGET_MODE_PAGE) {
            $href = \Contao\Controller::replaceInsertTags('{{link_url::' . $this->targetPageId . '}}');
        } elseif ($this->targetMode === self::TARGET_MODE_URL) {
            if (!C4GUtils::startsWith($this->targetPageUrl, 'http')) {
                $href = 'http://' . $this->targetPageUrl;
            } else {
                $href = $this->targetPageUrl;
            }
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
     * @return string
     */
    public function getTargetPageUrl(): string
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

    /**
     * @return bool
     */
    public function isConditional(): bool
    {
        return $this->conditional;
    }

    /**
     * @param bool $conditional
     * @return C4GLinkButtonField
     */
    public function setConditional(bool $conditional = true): C4GLinkButtonField
    {
        $this->conditional = $conditional;

        return $this;
    }
}
