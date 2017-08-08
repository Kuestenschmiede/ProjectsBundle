<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\ProjectBundle\Classes\Actions;

class C4GRedirectBackAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dialogParams = $this->getDialogParams();
        $redirectBackSite = $dialogParams->getRedirectBackSite();
        $id = $dialogParams->getId();

        if ($redirectBackSite && (($jumpTo = \PageModel::findByPk($redirectBackSite)) !== null)) {

            if ($dialogParams->isRedirectWithSaving()) {
                $this->saveDialog($id);
            }

            $return['jump_to_url'] = $jumpTo->getFrontendUrl();
        }

        return $return;
    }
}
