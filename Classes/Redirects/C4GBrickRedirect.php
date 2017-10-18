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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return int
     */
    public function getSite(): int
    {
        return $this->site;
    }

    /**
     * @param int $site
     */
    public function setSite(int $site)
    {
        $this->site = $site;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }
}