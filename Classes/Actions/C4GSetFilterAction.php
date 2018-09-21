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

class C4GSetFilterAction extends C4GBrickDialogAction
{
    public function run()
    {
        $dlgValues = $this->getPutVars();
        $dialogParams = $this->getDialogParams();
        $listParams = $this->getListParams();
        $filter = $listParams->getFilterObject();

        if ($filter) {
            $filter->setFilter($dlgValues, $this->module->getBrickKey());
            $action = new C4GShowListAction($dialogParams, $listParams, $this->getFieldList(), $dlgValues, $this->getBrickDatabase());
            return $action->run();
        }

        /** DEPRECATED; use a C4GListFilter object instead. */
        $filterParams = $listParams->getFilterParams();
        if ($filterParams) {
            if ($filterParams->isWithRangeFilter()) {
                $from = $dlgValues['fromFilter'];
                $to = $dlgValues['toFilter'];
                $DateTime_from = strtotime($from);
                $DateTime_to = strtotime($to);

                if ($DateTime_from > $DateTime_to) {
                    $from = $to;
                }

                $filterParams->setRangeFrom($from);
                $filterParams->setRangeTo($to);

                $filterParams->setBrickFilterCookies($this->getModule()->getBrickKey());
                $this->getListParams()->setFilterParams($filterParams);
            } elseif ($filterParams->isWithMethodFilter()) {
                $filterParams->toggleMethodFilter();
                $filterParams->setBrickFilterCookies($this->getModule()->getBrickKey());
            }

            $action = new C4GShowListAction($dialogParams, $this->getListParams(), $this->getFieldList(), $this->getPutVars(), $this->getBrickDatabase());
            return $action->run();
        }

        return null;
    }

    public function isReadOnly()
    {
        return true;
    }
}
