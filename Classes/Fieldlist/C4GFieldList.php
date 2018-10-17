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

namespace con4gis\ProjectsBundle\Classes\Fieldlist;


use con4gis\CoreBundle\Resources\contao\classes\container\C4GBaseContainer;
use con4gis\ProjectsBundle\Classes\Fieldtypes\C4GKeyField;

class C4GFieldList extends C4GBaseContainer
{
    protected $keyField = null;

    public function addField(C4GBrickField $field, string $key) {
        if ($this->keyField === null && $field instanceof C4GKeyField) {
            $this->keyField = $field;
        }
        return $this->add($field, $key);
    }

    public function deleteField($fieldName) {
        return $this->delete($fieldName);
    }

    public function getKeyField() {
        return $this->keyField;
    }
}