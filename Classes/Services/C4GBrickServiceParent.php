<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\ProjectBundle\Classes\Services;

class C4GBrickServiceParent extends \Controller
{

    protected $arrReturn = array();
    protected $blnDebugMode = false;

    public function __construct()
    {
        if ($this->Input->get('debug') && ($this->Input->get('debug')=='1' || $this->Input->get('debug')=='true'))
        {
            $this->blnDebugMode = true;
        }

        if (FE_USER_LOGGED_IN) {
            $this->import('FrontendUser', 'User');
            $this->User->authenticate();

        }
    }

    public function generate()
    {
        $strMethod = \Input::get('method');
        $strId     = \Input::get('id');

        if (method_exists($this, $strMethod))
        {
            if ($strId) {
                if ($this->$strMethod($strId))
                {
                    if ($this->arrReturn) {
                        return json_encode($this->arrReturn);
                    } else {
                        return false;
                    }
                }
            } else {
                if ($this->$strMethod())
                {
                    if ($this->arrReturn) {
                        return json_encode($this->arrReturn);
                    } else {
                        return false;
                    }
                }
            }

            //ToDo Language
            return json_encode($this->getErrorReturn('Fehler: ' . $strMethod));
        }
        else
        {
            return false;
        }

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
        $arrReturn = array();
        $arrReturn['error'] = true;
        $arrReturn['message'] = $strMessage;
        return $arrReturn;
    }
}