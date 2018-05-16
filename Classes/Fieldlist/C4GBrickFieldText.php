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

namespace con4gis\ProjectsBundle\Classes\Fieldlist;


abstract class C4GBrickFieldText extends C4GBrickField
{
    /**
     * Properties
     * @property string $pattern Regular expression this field's value must meet.
     * @property int $maxChars Maximum number of characters to be displayed in the list
     */
    //Todo Alle nötigen Properties aus BrickField und den Kindern hier einfügen
    //Todo Prüfen, ob alles funktioniert und erst danach die Properties aus BrickField und den Kindern löschen.


    protected $pattern = '';
    protected $maxChars = 0;

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
        if ($this->getAddStrBeforeValue()) {
            $value = $this->getAddStrBeforeValue().$value;
        }
        if ($this->getAddStrBehindValue()) {
            $value = $value.$this->getAddStrBehindValue();
        }

        //remove critical characters
        $value = htmlentities($value);

        //Cut field value if enabled and if it is too long
        if ($this->maxChars > 2) {
            if ($this->maxChars > 0 && (strlen($value) > $this->maxChars)) {
                $value = $this->cutFieldValue($value, $this->maxChars);
            }
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




}