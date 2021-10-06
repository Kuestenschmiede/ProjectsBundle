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
use Contao\StringUtil;

class C4GMultiLinkField extends C4GBrickField
{
    private $linkClass = '';
    private $wrapper = false;
    private $wrapperClass = 'c4g_condition__wrapper';

    public function getC4GDialogField($fieldList, $data, C4GBrickDialogParams $dialogParams, $additionalParams = [])
    {
        $id = 'c4g_' . $this->getFieldName();

        $value = $this->generateInitialValue($data);
        $result = '';

        if ($this->isShowIfEmpty() || !empty($value)) {
            $condition = $this->createConditionData($fieldList, $data);

            $tags = [];
            foreach (StringUtil::deserialize($value) as $link) {
                if ($link['linkHref'] === '' || $link['linkTitle'] === '') {
                    break;
                }

                if ($link['linkNewTab'] === '1') {
                    $rel = 'target="_blank" rel="noopener noreferrer"';
                } else {
                    $rel = '';
                }
                $tags[] = '<a class="'.$this->linkClass.'" href="' . $link['linkHref'] . "\" $rel>" . $link['linkTitle'] . '</a>';
            }

            if ($this->wrapper) {
                $fieldData = '<div class="'.$this->wrapperClass.'">'.implode('', $tags).'</div>';
            } else {
                $fieldData = implode('', $tags);
            }

            if (!empty($tags)) {
                $result = $this->addC4GField(
                    $condition,
                    $dialogParams,
                    $fieldList,
                    $data,
                    $fieldData
                );
            } else {
                $result = '';
            }
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
        if ($value === '' || $value === null) {
            return '';
        }

        $value = str_replace('=', '&#61;', $value);

        if ($value === '' && !$this->isShowIfEmpty()) {
            return '';
        }

        $tags = [];
        foreach (StringUtil::deserialize($value) as $link) {
            if ($link['linkNewTab'] === '1') {
                $rel = 'target="_blank" rel="noopener noreferrer" ';
            } else {
                $rel = '';
            }
            $tags[] = '<a href="' . $link['linkHref'] . "\" $rel onclick=\"event.stopPropagation();\">" . $link['linkTitle'] . '</a>';
        }

        return implode('', $tags);
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
     * @return string
     */
    public function getLinkClass(): string
    {
        return $this->linkClass;
    }

    /**
     * @param string $linkClass
     */
    public function setLinkClass(string $linkClass): void
    {
        $this->linkClass = $linkClass;
    }

    /**
     * @return bool
     */
    public function isWrapper(): bool
    {
        return $this->wrapper;
    }

    /**
     * @param bool $wrapper
     */
    public function setWrapper(bool $wrapper = true): void
    {
        $this->wrapper = $wrapper;
    }

    /**
     * @return string
     */
    public function getWrapperClass(): string
    {
        return $this->wrapperClass;
    }

    /**
     * @param string $wrapperClass
     */
    public function setWrapperClass(string $wrapperClass): void
    {
        $this->wrapperClass = $wrapperClass;
    }
}
