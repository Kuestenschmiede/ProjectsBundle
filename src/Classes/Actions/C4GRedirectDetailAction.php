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

class C4GRedirectDetailAction extends C4GRedirectAction
{
    private $redirectToDetail = true;
    private $redirectWithSaving = false;

    public function run()
    {
        $this->setRedirectSite($this->getListParams()->getRedirectTo());

        return parent::run();
    }

    /**
     * @return bool
     */
    public function isRedirectWithSaving()
    {
        return $this->redirectWithSaving;
    }

    /**
     * @param bool $redirectWithSaving
     * @return $this
     */
    public function setRedirectWithSaving($redirectWithSaving = true)
    {
        $this->redirectWithSaving = $redirectWithSaving;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRedirectToDetail()
    {
        return $this->redirectToDetail;
    }

    /**
     * @param bool $redirectToDetail
     * @return $this
     */
    public function setRedirectToDetail($redirectToDetail = true)
    {
        $this->redirectToDetail = $redirectToDetail;

        return $this;
    }
}
