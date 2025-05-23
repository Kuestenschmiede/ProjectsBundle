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
namespace con4gis\ProjectsBundle\Classes\Actions;

use con4gis\CoreBundle\Classes\C4GUtils;

class C4GReloadAction extends C4GBrickDialogAction
{
    private $jumpTo = '';

    public function run()
    {
        if (!$this->jumpTo) {
            $this->jumpTo = C4GUtils::replaceInsertTags('{{link_url::back}}');
        }
        $return['jump_to_url'] = $this->jumpTo;

        return $return;
    }

    public function isReadOnly()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getJumpTo(): string
    {
        return $this->jumpTo;
    }

    /**
     * @param string $jumpTo
     */
    public function setJumpTo(string $jumpTo): void
    {
        $this->jumpTo = $jumpTo;
    }
}
