<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GSaveAndRedirectDialogAction extends C4GSaveDialogAction
{
    protected $withRedirect = true;

    /**
     * @return bool
     */
    public function isWithRedirect(): bool
    {
        return $this->withRedirect;
    }

    /**
     * @param bool $withRedirect
     */
    public function setWithRedirect(bool $withRedirect): void
    {
        $this->withRedirect = $withRedirect;
    }
}
