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
namespace con4gis\ProjectsBundle\Classes\Fieldlist;

use Contao\Controller;

abstract class C4GBrickFieldText extends C4GBrickField
{
    /**
     * Properties
     * @property string $pattern Regular expression this field's value must meet.
     * @property int $maxChars Maximum number of characters to be displayed in the list
     * @property boolean $replaceInsertTag Should the replaceInsertTags function be called on the field value?
     */
    protected $pattern = '';
    protected $maxChars = 0;
    protected $replaceInsertTag = false;
    protected $encodeHtmlEntities = true;
    protected $placeholder = '';

    /**
     * Will be called by if the field value is longer than $maxChars. Return a value that will replace it.
     * This will not overwrite the value stored in the database.
     * @param $value
     * @param $maxChars
     * @return string
     */
    public function cutFieldValue($value, $maxChars)
    {
        return substr($value, 0, $maxChars - 3) . '...';
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

        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue() . $value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value . $this->getAddStrBehindValue();
        }

        //remove critical characters
        if ($this->encodeHtmlEntities === true) {
            $value = htmlentities($value);
        }

        //Cut field value if enabled and if it is too long
        if ($this->maxChars > 2) {
            if ($this->maxChars > 0 && (strlen($value) > $this->maxChars)) {
                $value = $this->cutFieldValue($value, $this->maxChars);
            }
        }
        if ($this->replaceInsertTag) {
            $value = Controller::replaceInsertTags($value);
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param $pattern
     * @return $this
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxChars()
    {
        return $this->maxChars;
    }

    /**
     * @param $maxChars
     * @return $this
     */
    public function setMaxChars($maxChars)
    {
        $this->maxChars = $maxChars;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReplaceInsertTag()
    {
        return $this->replaceInsertTag;
    }

    /**
     * @param bool $replaceInsertTag
     */
    public function setReplaceInsertTag($replaceInsertTag)
    {
        $this->replaceInsertTag = $replaceInsertTag;
    }

    /**
     * @return bool
     */
    public function isEncodeHtmlEntities(): bool
    {
        return $this->encodeHtmlEntities;
    }

    /**
     * @param bool $encodeHtmlEntities
     * @return C4GBrickFieldText
     */
    public function setEncodeHtmlEntities(bool $encodeHtmlEntities = true): C4GBrickFieldText
    {
        $this->encodeHtmlEntities = $encodeHtmlEntities;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     * @return C4GBrickFieldText
     */
    public function setPlaceholder(string $placeholder): C4GBrickFieldText
    {
        $this->placeholder = $placeholder;

        return $this;
    }
}
