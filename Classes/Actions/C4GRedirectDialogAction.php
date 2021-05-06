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

class C4GRedirectDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $return['ussermessage'] = 'Fehler bei der Weiterleitung'; //ToDo Language
        $redirects = $dialogParams->getRedirects();
        $pageId = 0;

        if ($redirects) {
            //ToDo active state lost
            foreach ($redirects as $redirect) {
                $pageId = $redirect->getSite();
                if ($redirect->isActive()) {
                    break;
                }
            }
        }

        if ($pageId && (($jumpTo = \PageModel::findByPk($pageId)) !== null)) {
            $return['jump_to_url'] = $jumpTo->getFrontendUrl();
        }

        return $return;
    }

    public function isReadOnly()
    {
        return true;
    }
}
