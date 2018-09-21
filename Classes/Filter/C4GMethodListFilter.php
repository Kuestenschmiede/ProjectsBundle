<?php
/**
 * Created by PhpStorm.
 * User: rro
 * Date: 21.09.18
 * Time: 09:31
 */

namespace con4gis\ProjectsBundle\Classes\Filter;


use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;

class C4GMethodListFilter extends C4GListFilter
{
    private $on = 0;                        //If the filter should currently be applied.
    private $callMode = self::STATIC;       //Whether to call the filter method statically or in object context.
    private $methodClass = '';              //Class string for static calls.
    private $methodObject = null;           //Object to call the method on when in object context.
    private $method = '';                   //Method string to call. Takes the list elements and dialog params as parameters.
    private $switchOnButtonCaption = '';    //The text in the "apply-filter-button" shown if the filter is off.
    private $switchOffButtonCaption = '';   //The text in the "remove-filter--button" shown if the filter is on.

    const STATIC = 'static';
    const OBJECT = 'object';

    /**
     * @param $dlgValues
     * @param $brickKey
     */
    public function setFilter($dlgValues, $brickKey)
    {
        if ($this->on === 0) {
            $this->on = 1;
        } else {
            $this->on = 0;
        }
        $this->setFilterCookies($brickKey);
    }

    /**
     * Filter out undesired elements and return the desired ones.
     * @param $elements
     * @param $dialogParams
     * @return mixed
     */
    public function filter($elements, $dialogParams)
    {
        if ($this->on) {
            if ($this->callMode === self::STATIC) {
                $class = $this->methodClass;
                $method = $this->method;
                return $class::$method($elements, $dialogParams);
            } else {
                $object = $this->methodObject;
                $method = $this->method;
                return $object->$method($elements, $dialogParams);
            }
        } else {
            return $elements;
        }
    }

    /**
     * Call listParams->addButton() to dynamically add the filter button to the list.
     * @param $listParams
     */
    public function addButton($listParams)
    {
        if ($this->on === 1) {
            $listParams->addButton(C4GBrickConst::BUTTON_TOGGLE_METHOD_FILTER, $this->switchOffButtonCaption);
        } else {
            $listParams->addButton(C4GBrickConst::BUTTON_TOGGLE_METHOD_FILTER, $this->switchOnButtonCaption);
        }
    }

    /**
     * @param $brickKey
     */
    protected function setFilterCookies($brickKey)
    {
        setcookie($brickKey.'_methodFilter', $this->on, time()+3600, '/');
    }

    /**
     * @param $brickKey
     */
    public function getFilterCookies($brickKey)
    {
        $methodFilterCookie = $_COOKIE[$brickKey.'_methodFilter'];
        if ($methodFilterCookie === '1' || $methodFilterCookie === 1) {
            $this->on = 1;
        } else {
            $this->on = 0;
        }
    }

    /**
     * @return string
     */
    public function getCallMode(): string
    {
        return $this->callMode;
    }

    /**
     * @param string $callMode
     * @return C4GMethodListFilter
     */
    public function setCallMode(string $callMode): C4GMethodListFilter
    {
        $this->callMode = $callMode;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethodClass(): string
    {
        return $this->methodClass;
    }

    /**
     * @param string $methodClass
     * @return C4GMethodListFilter
     */
    public function setMethodClass(string $methodClass): C4GMethodListFilter
    {
        $this->methodClass = $methodClass;
        return $this;
    }

    /**
     * @return null
     */
    public function getMethodObject()
    {
        return $this->methodObject;
    }

    /**
     * @param $methodObject
     * @return C4GMethodListFilter
     * @throws \Exception
     */
    public function setMethodObject($methodObject): C4GMethodListFilter
    {
        if (is_object($methodObject)) {
            $this->methodObject = $methodObject;
        } else {
            throw new \Exception('C4GMethodListFilter::setMethodObject() must be given an object, ' . gettype($methodObject) . ' given. Did you mean to call setMethodClass()?');
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return C4GMethodListFilter
     */
    public function setMethod(string $method): C4GMethodListFilter
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return string
     */
    public function getSwitchOnButtonCaption(): string
    {
        return $this->switchOnButtonCaption;
    }

    /**
     * @param string $switchOnButtonCaption
     * @return C4GMethodListFilter
     */
    public function setSwitchOnButtonCaption(string $switchOnButtonCaption): C4GMethodListFilter
    {
        $this->switchOnButtonCaption = $switchOnButtonCaption;
        return $this;
    }

    /**
     * @return string
     */
    public function getSwitchOffButtonCaption(): string
    {
        return $this->switchOffButtonCaption;
    }

    /**
     * @param string $switchOffButtonCaption
     * @return C4GMethodListFilter
     */
    public function setSwitchOffButtonCaption(string $switchOffButtonCaption): C4GMethodListFilter
    {
        $this->switchOffButtonCaption = $switchOffButtonCaption;
        return $this;
    }
}