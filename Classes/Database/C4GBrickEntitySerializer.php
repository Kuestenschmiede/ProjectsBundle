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
namespace con4gis\ProjectsBundle\Classes\Database;

use Doctrine\ORM\EntityManager;

/**
 * Class C4GBrickEntitySerializer
 * @package c4g\projects
 */
class C4GBrickEntitySerializer
{
    private $entityManager;

    /**
     * C4GBrickEntitySerializer constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $entityObject
     * @return \stdClass
     */
    public function serialize($entityObject)
    {
        $data = new \stdClass();

        $className = get_class($entityObject);
        $metaData = $this->entityManager->getClassMetadata($className);

        foreach ($metaData->fieldMappings as $field => $mapping)
        {
            //ToDo bei boolean "is"?
            $method = "get" . ucfirst($field);
            $data->$field = call_user_func(array($entityObject, $method));
        }

        foreach ($metaData->associationMappings as $field => $mapping)
        {
            $object = $metaData->reflFields[$field]->getValue($entityObject);
            $data->$field = $this->serialize($object);
        }

        return $data;
    }


    public function unserialize($contaoSet, $entityObject = null)
    {
        if ($contaoSet && $entityObject) {
            foreach ($contaoSet as $field => $value) {
                $method = "set" . ucfirst($field);
                call_user_func(array($entityObject, $method), $value);
            }
        }

        return $entityObject;
    }
}