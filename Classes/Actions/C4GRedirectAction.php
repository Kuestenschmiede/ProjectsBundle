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
namespace con4gis\ProjectsBundle\Classes\Actions;

class C4GRedirectAction extends C4GBrickDialogAction
{
    private $redirectWithAction = '';
    private $redirectToDetail = false;
    private $setParentIdAfterSave = false;
    private $setSessionIdAfterInsert = '';
    private $redirectSite = '';
    private $redirectWithSaving = true;

    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $id = $dialogParams->getId();
        if (!$this->redirectSite) {
            $this->redirectSite = $dialogParams->getRedirectSite();
        }
        if ($this->redirectSite && (($jumpTo = \PageModel::findByPk($this->redirectSite)) !== null)) {
            if ($this->redirectWithSaving && $dialogParams->isRedirectWithSaving() && !$dialogParams->isRedirectWithActivation()) {
                $action = new C4GSaveDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->putVars, $this->getBrickDatabase());
                if ($this->setParentIdAfterSave) {
                    $action->setSetParentIdAfterSave(true);
                }
                if ($this->setSessionIdAfterInsert) {
                    $action->setSetSessionIdAfterInsert($this->setSessionIdAfterInsert);
                }
                $action->setModule($this->getModule());
                $return = $action->run();
            }

//            if ($dialogParams->isRedirectWithActivation()) {
//                $action = new C4GActivationDialogAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->putVars, $this->getBrickDatabase());
//                $return = $action->run();
//            }

            // only set parent id if it's a new dataset, otherwise it will be set already
            if ($this->setParentIdAfterSave && $dialogParams->getParentId() && $id == -1) {
                $this->redirectWithAction .= $dialogParams->getParentId();
            }
            if (!$return['usermessage']) {
                $return['jump_to_url'] = $jumpTo->getFrontendUrl();
                if ($this->getRedirectWithAction) {
                    $return['jump_to_url'] .= '?' . $this->redirectWithAction;
                }
                if ($this->isRedirectToDetail()) {
                    $return['jump_to_url'] .= '?state=item:' . $id;
                }
            }
        }

        return $return;
    }

    /**
     * @return string
     */
    public function getRedirectWithAction()
    {
        return $this->redirectWithAction;
    }

    /**
     * @param $redirectWithAction
     * @return $this
     */
    public function setRedirectWithAction($redirectWithAction)
    {
        $this->redirectWithAction = $redirectWithAction;

        return $this;
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
     * @return $this
     */
    public function setSetParentIdAfterSave($setParentIdAfterSave = true)
    {
        $this->setParentIdAfterSave = $setParentIdAfterSave;

        return $this;
    }

    /**
     * @return string
     */
    public function getRedirectSite()
    {
        return $this->redirectSite;
    }

    /**
     * @param $redirectSite
     * @return $this
     */
    public function setRedirectSite($redirectSite)
    {
        $this->redirectSite = $redirectSite;

        return $this;
    }

    /**
     * @return string
     */
    public function getSetSessionIdAfterInsert()
    {
        return $this->setSessionIdAfterInsert;
    }

    /**
     * @param $setSessionIdAfterInsert
     * @return $this
     */
    public function setSetSessionIdAfterInsert($setSessionIdAfterInsert)
    {
        $this->setSessionIdAfterInsert = $setSessionIdAfterInsert;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRedirectWithSaving()
    {
        return $this->redirectWithSaving;
    }

    /**
     * @param $redirectWithSaving
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
     * @param $redirectToDetail
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
