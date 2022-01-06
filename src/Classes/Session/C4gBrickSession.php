<?php

namespace con4gis\ProjectsBundle\Classes\Session;

use Symfony\Component\HttpFoundation\Session\Session;

class C4gBrickSession
{
    private Session $session;

    public function __construct(Session &$session)
    {
        $this->session = $session;
    }

    /**
     * @param $key
     * @return array|string
     */
    public function getSessionValue($key) {
        if ($this->session->isStarted())
        {
            if ($this->session->has($key))
            {
                return $this->session->get($key);
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     */
    public function setSessionValue($key, $value) {
        if ($this->session->isStarted())
        {
            $this->session->set($key, $value);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $key
     * @return void
     */
    public function remove($key) {
        $this->session->remove($key);
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

}