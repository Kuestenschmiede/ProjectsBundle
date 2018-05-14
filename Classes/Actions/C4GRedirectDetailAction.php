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

namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GRedirectDetailAction extends C4GBrickDialogAction
{
    private $redirectToDetail = true;
    private $redirectWithSaving = false;

    public function run()
    {
        $this->setRedirectSite($this->listParams->getRedirectTo());
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

    public function isReadOnly()
    {
        return true;
    }

}
