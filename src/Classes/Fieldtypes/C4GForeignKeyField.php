<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Fieldtypes;

use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBaseKeyField;
use con4gis\ProjectsBundle\Classes\Fieldlist\C4GBrickFieldType;

class C4GForeignKeyField extends C4GBaseKeyField
{
    /**
     * @param string $type
     */
    public function __construct(string $type = C4GBrickFieldType::FOREIGNKEY)
    {
        $this->setEditable();
        $this->setDatabaseField();
        parent::__construct($type);
    }

}
