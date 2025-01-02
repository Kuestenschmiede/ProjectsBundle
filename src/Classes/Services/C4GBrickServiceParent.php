<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Services;

use Contao\System;
use Contao\FrontendUser;
use Contao\Input;

class C4GBrickServiceParent extends \Controller
{
    protected $arrReturn = [];
    protected $blnDebugMode = false;

    public function __construct()
    {
        $hasFrontendUser = System::getContainer()->get('contao.security.token_checker')->hasFrontendUser();
        if ($this->Input->get('debug') && ($this->Input->get('debug') == '1' || $this->Input->get('debug') == 'true')) {
            $this->blnDebugMode = true;
        }

        if ($hasFrontendUser) {
            $this->import(FrontendUser::class, 'User');
            $this->User->authenticate();
        }
    }

    public function generate()
    {
        $strMethod = Input::get('method');
        $strId = Input::get('id');

        if (method_exists($this, $strMethod)) {
            if ($strId) {
                if ($this->$strMethod($strId)) {
                    if ($this->arrReturn) {
                        return json_encode($this->arrReturn);
                    }

                    return false;
                }
            } else {
                if ($this->$strMethod()) {
                    if ($this->arrReturn) {
                        return json_encode($this->arrReturn);
                    }

                    return false;
                }
            }

            //ToDo Language
            return json_encode($this->getErrorReturn('Fehler: ' . $strMethod));
        }

        return false;
    }

    protected function getPosition($id)
    {
        //WIRD IM MODUL überschrieben

        return false;
    }

    protected function getPositions()
    {
        //WIRD IM MODUL überschrieben

        return false;
    }

    protected function pushMessage()
    {
        //WIRD IM MODUL überschrieben

        return false;
    }

    private function getErrorReturn($strMessage)
    {
        $arrReturn = [];
        $arrReturn['error'] = true;
        $arrReturn['message'] = $strMessage;

        return $arrReturn;
    }
}
