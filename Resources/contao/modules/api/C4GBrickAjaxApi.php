<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
class C4GBrickAjaxApi extends \Frontend
{
    /**
     * generates the module
     * @param  array $arrInput [description]
     * @return mixed           [description]
     */
    public function generate($arrInput)
    {
        $this->import('FrontendUser', 'User');
        $this->User->authenticate();

        $id = $arrInput[0]?:null;
        $req = $arrInput[1]?:null;
        // switch ($_SERVER['REQUEST_METHOD'])
        // {
        // 	case 'GET':
        // 		return $this->get( $id, $req );
        // 	case 'PUT':
        // 		return $this->get( $id, $req );
        // 	case 'POST':
        // 		return 'GET-' . print_r( $_POST, true );
        // 	default:
        // 		header('HTTP/1.1 405 Method Not Allowed');
        // 		die;
        // }

        if (isset( $id ) && is_numeric( $id )) {
            return $this->getC4gFrontendModule( $id, $req );
        } else {
            header('HTTP/1.1 400 Bad Request');
            die;
        }
    }

    //comment
    private function get( $id = null, $req = null )
    {
        if (!isset( $id )) {
            #pass
        } elseif (is_numeric( $id )) {
            #pass
        } else {
            header('HTTP/1.1 400 Bad Request');
            die;
        }
    }

    /**
     * Generate a front end module and return it as HTML string
     * @param integer
     * @param string
     * @return string
     */
    protected function getC4gFrontendModule($intId, $req=null)
    {
        if (!strlen($intId) || $intId < 1)
        {
            header('HTTP/1.1 412 Precondition Failed');
            return 'Missing frontend module ID';
        }

        $objModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
            ->limit(1)
            ->execute($intId);

        if ($objModule->numRows < 1)
        {
            header('HTTP/1.1 404 Not Found');
            return 'Frontend module not found';
        }

        // Show to guests only
        if ($objModule->guests && FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN && !$objModule->protected)
        {
            header('HTTP/1.1 403 Forbidden');
            return 'Forbidden';
        }

        // Protected element
        if (!BE_USER_LOGGED_IN && $objModule->protected)
        {
            if (!FE_USER_LOGGED_IN)
            {
                header('HTTP/1.1 403 Forbidden');
                return 'Forbidden';
            }

            $this->import('FrontendUser', 'User');
            $groups = deserialize($objModule->groups);

            if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1)
            {
                header('HTTP/1.1 403 Forbidden');
                return 'Forbidden';
            }
        }

        $strClass = $this->findFrontendModule($objModule->type);

        // Return if the class does not exist
        if (!$this->classFileExists($strClass))
        {
            $this->log('Module class "'.$GLOBALS['FE_MOD'][$objModule->type].'" (module "'.$objModule->type.'") does not exist', 'Ajax getFrontendModule()', TL_ERROR);

            header('HTTP/1.1 404 Not Found');
            return 'Frontend module class does not exist';
        }

        $objModule->typePrefix = 'mod_';
        $objModule = new $strClass($objModule, $strColumn);

        return $objModule->generateAjax( $req );
    }
//    /**
//     * generates the module
//     * @param  array $arrInput [description]
//     * @return mixed           [description]
//     */
//    public function generate($arrInput)
//    {
//        $this->import('FrontendUser', 'User');
//        $this->User->authenticate();
//
//        $id = $arrInput[0]?:null;
//        $req = $arrInput[1]?:null;
//        switch ($_SERVER['REQUEST_METHOD'])
//        {
////         	case 'GET':
////                if ( ($id) && (strpos($id,'=') !== false) ) {
////                    $values = explode('=', $id, 2);
////                    $method = $values[1];
////
////                    if ($method) {
////                        //try {
////                            return \c4g\projects\C4GBrickServiceParent::$method();
////                        //} catch (Exception $e) {
////                            //function exists?
////                        //}
////                    }
////                }
//          	  //return $this->get( $id, $req );
//        //  case 'PUT':
//        // 	    return $this->get( $id, $req );
//        //  case 'POST':
//        //  	return 'GET-' . print_r( $_POST, true );
//        // 	default:
//        // 		header('HTTP/1.1 405 Method Not Allowed');
//        // 		die;
//        }
//
//        if (isset( $id ) && is_numeric( $id )) {
//            return $this->getC4GFrontendModule( $id, $req );
//        } else {
//            header('HTTP/1.1 400 Bad Request');
//            die;
//        }
//
//
//    }
//
//    //comment
//    private function get( $id = null, $req = null )
//    {
//        if (!isset( $id )) {
//            #pass
//        } elseif (is_numeric( $id )) {
//            #pass
//        } else {
//            header('HTTP/1.1 400 Bad Request');
//            die;
//        }
//    }
//
//    /**
//     * from "Ajax.php" (by A. Schempp)
//     * Generate a front end module and return it as HTML string
//     * @param integer
//     * @param string
//     * @return string
//     */
//    protected function getC4GFrontendModule($intId, $req=null)
//    {
//        if (!strlen($intId) || $intId < 1)
//        {
//            header('HTTP/1.1 412 Precondition Failed');
//            return 'Missing frontend module ID';
//        }
//
//        $objModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
//            ->limit(1)
//            ->execute($intId);
//
//        if ($objModule->numRows < 1)
//        {
//            header('HTTP/1.1 404 Not Found');
//            return 'Frontend module not found';
//        }
//
//        // Show to guests only
//        if ($objModule->guests && FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN && !$objModule->protected)
//        {
//            header('HTTP/1.1 403 Forbidden');
//            return 'Forbidden';
//        }
//
//        // Protected element
//        if (!BE_USER_LOGGED_IN && $objModule->protected)
//        {
//            if (!FE_USER_LOGGED_IN)
//            {
//                header('HTTP/1.1 403 Forbidden');
//                return 'Forbidden';
//            }
//
//            $this->import('FrontendUser', 'User');
//            $groups = deserialize($objModule->groups);
//
//            if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1)
//            {
//                header('HTTP/1.1 403 Forbidden');
//                return 'Forbidden';
//            }
//        }
//
//        $strClass = $this->findFrontendModule($objModule->type);
//
//        // Return if the class does not exist
//        if (!$this->classFileExists($strClass))
//        {
//            $this->log('Module class "'.$GLOBALS['FE_MOD'][$objModule->type].'" (module "'.$objModule->type.'") does not exist', 'Ajax getFrontendModule()', TL_ERROR);
//
//            header('HTTP/1.1 404 Not Found');
//            return 'Frontend module class does not exist';
//        }
//
//        $objModule->typePrefix = 'mod_';
//        $objModule = new $strClass($objModule, null); //vorher nicht null sondern $strColumn
//
//        return $objModule->generateAjax( $req );
//    }
}