<?php

/**
 * Created by PhpStorm.
 * User: cro
 * Date: 11.10.2016
 * Time: 10:33
 */
namespace con4gis\ProjectsBundle\Classes\Framework;

class C4GContainer
{
    private static $instance;
    protected $container;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new C4GContainer();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public function addService($path, $file = 'service.yml') {
        $loader = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($this->container, new \Symfony\Component\Config\FileLocator($path));
        $loader->load($file);
    }

    public function getService($service) {
        return $this->container->get($service);
    }
}