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

        if ( $pageId && (($jumpTo = \PageModel::findByPk( $pageId)) !== null)) {
            $return['jump_to_url'] = $jumpTo->getFrontendUrl();
        }

        return $return;
    }
}
