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
     * @return string
     */
    public function getRedirectWithAction()
    {
        return $this->redirectWithAction;
    }

    /**
     * @param string $redirectWithAction
     */
    public function setRedirectWithAction($redirectWithAction)
    {
        $this->redirectWithAction = $redirectWithAction;
    }

    /**
     * @return bool
     */
    public function isSetParentIdAfterSave()
    {
        return $this->setParentIdAfterSave;
    }

    /**
     * @param bool $setParentIdAfterSave
     */
    public function setSetParentIdAfterSave($setParentIdAfterSave)
    {
        $this->setParentIdAfterSave = $setParentIdAfterSave;
    }

    /**
     * @return string
     */
    public function getRedirectSite()
    {
        return $this->redirectSite;
    }

    /**
     * @param string $redirectSite
     */
    public function setRedirectSite($redirectSite)
    {
        $this->redirectSite = $redirectSite;
    }

    /**
     * @return string
     */
    public function getSetSessionIdAfterInsert()
    {
        return $this->setSessionIdAfterInsert;
    }

    /**
     * @param string $setSessionIdAfterInsert
     */
    public function setSetSessionIdAfterInsert($setSessionIdAfterInsert)
    {
        $this->setSessionIdAfterInsert = $setSessionIdAfterInsert;
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
     */
    public function setRedirectWithSaving($redirectWithSaving)
    {
        $this->redirectWithSaving = $redirectWithSaving;
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
     */
    public function setRedirectToDetail($redirectToDetail)
    {
        $this->redirectToDetail = $redirectToDetail;
    }

}
