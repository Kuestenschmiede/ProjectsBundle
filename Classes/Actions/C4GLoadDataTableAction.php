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


use Symfony\Component\HttpFoundation\JsonResponse;

class C4GLoadDataTableAction extends C4GBrickAction
{
    public function run()
    {
        // TODO: Implement run() method.

        $dataArray = array();

        //Load the data from the database as an associative array
        $row = array();

        foreach ($row as $r) {
            $dataArray[] = $r;
        }

        $response = array('data' => $dataArray);

        return new JsonResponse($response);
    }

    public function isReadOnly()
    {
        return true;
    }

}