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
namespace con4gis\ProjectsBundle\Classes\Redirects;

use con4gis\ProjectsBundle\Classes\Common\C4GBrickConst;

class C4GBrickRedirect
{
    private $type = C4GBrickConst::REDIRECT_DEFAULT;
    private $title = '';
    private $message = '';
    private $site = 0;
    private $active = false;

    /**
     * C4GBrickRedirect constructor.
     * @param string $type
     * @param string $title
     * @param string $message
     * @param int $site
     */
    public function __construct($type, $title, $message, $site)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param int $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }
}
