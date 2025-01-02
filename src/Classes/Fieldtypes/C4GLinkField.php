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
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GLinkField extends C4GBrickField
{
    protected $linkLabel = '';
    protected $labelField = '';
    protected $linkType = self::LINK_TYPE_DEFAULT;
    protected $newTab = false;

    const LINK_TYPE_DEFAULT = 10;
    const LINK_TYPE_PHONE = 20;
    const LINK_TYPE_EMAIL = 30;

    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::LINK)
    {
        parent::__construct($type);
        $this->setDatabaseField(true);
        $this->setEditable(false);
    }

    /**
     * @param $field
     * @param $data
     * @return string
     */
    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();

        $beforeValue = $this->getAddStrBeforeValue();
        $this->setAddStrBeforeValue('');
        $afterValue = $this->getAddStrBehindValue();
        $this->setAddStrBehindValue('');

        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            switch ($this->linkType) {
                case self::LINK_TYPE_PHONE:
                    $href = 'tel:' . $value;

                    break;
                case self::LINK_TYPE_EMAIL:
                    $href = 'mailto:' . $value;

                    break;
                default:
                    if ($value !== '') {
                        if (!C4GUtils::startsWith($value, 'http')) {
                            $href = 'http://' . $value;
                        } else {
                            $href = $value;
                        }
                    }

                    break;
            }

            if ($this->newTab) {
                $rel = "target='_blank' rel='noopener noreferrer' ";
            } else {
                $rel = '';
            }

            if ($this->labelField !== '') {
                $labelFieldName = $this->labelField;
                $label = $data->$labelFieldName;
            } else {
                $label = '';
            }

            $label = $label ?: $this->linkLabel ?: $value;

            $result =
                $this->addC4GField($condition,$dialogParams,$fieldList,$data,
                '<a ' . $rel . ' ' . $condition['conditionPrepare'] .
                ' id="' . $id . '" class="formdata ' . $id . ' c4g_brick_link" href="' .
                $href . '">' . $beforeValue . $label . $afterValue . '</a>');
        }

        return $result;
    }

    /**
     * Public method for creating the field specific list HTML
     * @param $rowData
     * @param $content
     * @return mixed
     */
    public function getC4GListField($rowData, $content)
    {
        $fieldName = $this->getFieldName();

        $value = $rowData->$fieldName;

        if ($value === '' && !$this->isShowIfEmpty()) {
            return '';
        }

        switch ($this->linkType) {
            case self::LINK_TYPE_PHONE:
                $href = 'tel:' . $value;

                break;
            case self::LINK_TYPE_EMAIL:
                $href = 'mailto:' . $value;

                break;
            default:
                if (!C4GUtils::startsWith($value, 'http')) {
                    $href = 'http://' . $value;
                } else {
                    $href = $value;
                }

                break;
        }

        if ($this->labelField !== '') {
            $labelFieldName = $this->labelField;
            $label = $rowData->$labelFieldName;
        } else {
            $label = '';
        }

        $label = $label ?: $this->linkLabel ?: $value;

        if ($label !== '') {
            if ($this->getItemprop() && $rowData->itemType) {
                $label = '<span itemprop="' . $this->getItemProp() . "\">$label</span>";
            } else {
                $label = "<span>$label</span>";
            }
        }


        if ($this->newTab) {
            $rel = 'target="_blank" rel="noopener noreferrer"';
        } else {
            $rel = '';
        }
        $strReturn = '<a ' . $rel . ' href="' . $href . '" onclick="event.stopPropagation()">' . $label . '</a>';

        if ($this->getAddStrBeforeValue()) {
            $strReturn = '<span>' . $this->getAddStrBeforeValue() . '</span>' . $strReturn;
        }
        if ($this->getAddStrBehindValue()) {
            $strReturn = $strReturn . '<span>' . $this->getAddStrBehindValue() . '</span>';
        }
        return $strReturn;
    }

    /**
     * @param $linkLabel
     * @return $this
     */
    public function setLinkLabel($linkLabel): C4GLinkField
    {
        $this->linkLabel = $linkLabel;

        return $this;
    }

    /**
     * @param int $linkType
     * @return C4GLinkField
     */
    public function setLinkType(int $linkType): C4GLinkField
    {
        $this->linkType = $linkType;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelField(): string
    {
        return $this->labelField;
    }

    /**
     * @param string $labelField
     * @return C4GLinkField
     */
    public function setLabelField(string $labelField): C4GLinkField
    {
        $this->labelField = $labelField;

        return $this;
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

    /**
     * @return bool
     */
    public function isNewTab(): bool
    {
        return $this->newTab;
    }

    /**
     * @param bool $newTab
     * @return C4GLinkField
     */
    public function setNewTab(bool $newTab = true): C4GLinkField
    {
        $this->newTab = $newTab;

        return $this;
    }
}
