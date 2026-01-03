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

class C4GRedirectBackAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $redirectBackSite = $dialogParams->getRedirectBackSite();
        $id = $dialogParams->getId();

        if ($redirectBackSite && (($jumpTo = \Contao\PageModel::findByPk($redirectBackSite)) !== null)) {
            if ($dialogParams->isRedirectWithSaving()) {
                $this->saveDialog($id);
            }

            $return['jump_to_url'] = $jumpTo->getFrontendUrl();
        }

        return $return;
    }

    public function isReadOnly()
    {
        return true;
    }
}
