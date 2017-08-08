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

class C4GShowFilterDialogAction extends C4GBrickDialogAction
{
    public function run()
    {
        $filterParams = $this->getListParams()->getFilterParams();
        $dialogParams = $this->getDialogParams();
        $dialogParams->setMemberId($dialogParams->getMemberId());
        $dialogParams->setFilterParams($filterParams);

        $dialog = new \c4g\projects\C4GBrickFilterDialog($dialogParams);
        return $dialog->show();
    }
}
