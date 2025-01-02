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
namespace con4gis\ProjectsBundle\Classes\Framework;

class C4GContainer
{
    private static $instance;
    protected $container;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new C4GContainer();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function addService($path, $file = 'service.yml')
    {
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($this->container, new \Symfony\Component\Config\FileLocator($path));
        $loader->load($file);
    }

    public function getService($service)
    {
        return $this->container->get($service);
    }
}
