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
        if ( $this->redirectSite && (($jumpTo = \PageModel::findByPk( $this->redirectSite)) !== null)) {

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
                if ($this->redirectWithAction) {
                    $return['jump_to_url'] .= '?' . $this->redirectWithAction;
                }
                if ($this->redirectToDetail) {
                    $return['jump_to_url'] .= '?state=click:' . $id;

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

    public function isReadOnly()
    {
        return true;
    }

}
