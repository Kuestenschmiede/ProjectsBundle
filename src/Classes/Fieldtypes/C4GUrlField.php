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

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\ProjectsBundle\Classes\Dialogs\C4GBrickDialogParams;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldCompare;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GUrlField extends C4GBrickField
{
    private $withLink = true;
    private $addProtocol = true;
    private $linkType = self::LINK_TYPE_DEFAULT;
    private $url = ''; //optional additional to initial value

    const LINK_TYPE_DEFAULT = 10;
    const LINK_TYPE_PHONE = 20;
    const LINK_TYPE_EMAIL = 30;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::URL)
    {
        parent::__construct($type);
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();
        $required = $this->generateRequiredString($data, $dialogParams, $fieldList);
        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);
            $conditionPrepare = ' '. $condition['conditionPrepare'];
            $fieldDataBefore = '';
            $fieldDataAfter = '';

            if ($this->withLink && !$this->isEditable()) {
                $url = $this->url ?: $value;

                switch ($this->linkType) {
                    case self::LINK_TYPE_PHONE:
                        $url = 'tel:' . $url;

                        break;
                    case self::LINK_TYPE_EMAIL:
                        $url = 'mailto:' . $url;

                        break;
                    default:
                        if ($this->addProtocol && !C4GUtils::startsWith($url, 'http')) {
                            $url = 'https://' . $url;
                        }

                        break;
                }
                $conditionPrepare = '';
                $fieldDataBefore = '<a' . $conditionPrepare . ' href="' . $url . '" target="_blank" rel="noopener" class="noformdata">';
                $fieldDataAfter = '</a>';


            };

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                    $fieldDataBefore . '<input type="url" ' . $required . $conditionPrepare . ' id="' . $id . '" class="noformdata c4g__form-control c4g__form-url-input" name="' . $this->getFieldName() . '" title="' . $this->getTitle() . '" value="' . $value . '">' . $fieldDataAfter);
        }

        return $result;
    }

    /**
     * Method that will be called in the compareWithDB() in C4GBrickDialog
     * @param $dbValue
     * @param $dlgvalue
     * @return array
     */
    public function compareWithDB($dbValues, $dlgValues)
    {
        $fieldname = $this->getFieldName();
        $dbValue = $dbValues->$fieldname;
        $dlgvalue = $dlgValues[$this->getFieldName()];
        $dbValue = trim($dbValue);
        $dlgValue = trim($dlgvalue);
        $result = null;
        if (strcmp($dbValue, $dlgValue) != 0) {
            $result = new C4GBrickFieldCompare($this, $dbValue, $dlgValue);
        }

        return $result;
    }

    /**
     * @return bool
     */
    public function isWithLink()
    {
        return $this->withLink;
    }

    /**
     * @param $withLink
     * @return $this
     */
    public function setWithLink($withLink)
    {
        $this->withLink = $withLink;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAddProtocol(): bool
    {
        return $this->addProtocol;
    }

    /**
     * @param bool $addProtocol
     */
    public function setAddProtocol(bool $addProtocol): void
    {
        $this->addProtocol = $addProtocol;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getLinkType(): int
    {
        return $this->linkType;
    }

    /**
     * @param int $linkType
     */
    public function setLinkType(int $linkType): void
    {
        $this->linkType = $linkType;
    }
}
