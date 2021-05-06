<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\ProjectsBundle\Classes\Database;

use Doctrine\ORM\EntityManager;

/**
 * Class C4GBrickDatabase
 * @package c4g\projects
 */
class C4GBrickDatabase
{
    private $params = null;
    private $entityManager = null;

    /**
     * C4GBrickDatabase constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;

        if ($params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            $this->entityManager = \Contao\System::getContainer()->get('doctrine.orm.default_entity_manager');
        }
    }

    /**
     * @param $dataset
     * @return array
     */
    public function entitiesToContao($dataset)
    {
        $arrModels = [];

        $serializer = new C4GBrickEntitySerializer($this->entityManager);

        if (is_array($dataset)) {
            foreach ($dataset as $row) {
                $arrRow = $serializer->serialize($row);
                $arrModels[] = $arrRow;
            }
            $result = $arrModels;

            return $result;
        }

        return $serializer->serialize($dataset);
    }

    /**
     * @param $dataset
     * @return array
     */
    private function contaoToEntity($dataset)
    {
        $serializer = new C4GBrickEntitySerializer($this->entityManager);

        $pkField = $this->params->getPkField();

        $entityClass = $this->params->getEntityNamespace() . '\\' . $this->params->getEntityClass();
        $repo = $this->entityManager->getRepository($entityClass);

        if ($dataset['id']) {
            $id = $dataset['id'];
            $entityObject = $repo->findOneBy([$pkField => $id]);

            return $serializer->unserialize($dataset, $entityObject);
        }
        $entiy = new $entityClass();
        $entiy->setData($dataset);

        return $entiy;

        #return $serializer->unserialize($dataset, $entityObject);
    }

    /**
     * @param $fieldname
     * @param $value
     * @param array $arrOptions
     * @return array
     */
    public function findBy($fieldname, $value, $arrOptions = [])
    {
        if ($this->params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            //ToDo arrOptions abbilden
            $entityClass = $this->params->getEntityNamespace() . '\\' . $this->params->getEntityClass();

            return $this->entitiesToContao($this->entityManager->getRepository($entityClass)->findBy([$fieldname => $value]));
        }
        $modelClass = $this->params->getModelClass();
        if ($modelClass && method_exists($modelClass, 'findBy')) {
            return $modelClass::findBy($fieldname, $value, $arrOptions);
        }

        return [];
    }

    /**
     *
     */
    public function findAll()
    {
        if ($this->params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            $entityClass = $this->params->getEntityNamespace() . '\\' . $this->params->getEntityClass();

            return $this->entitiesToContao($this->entityManager->getRepository($entityClass)->findAll());
        }
        $modelClass = $this->params->getModelClass();
        if ($modelClass) {
            return $modelClass::findAll();
        }

        return [];
    }

    /**
     * @param $pk
     * @return array
     */
    public function findByPk($pk)
    {
        if (!$pk || $pk <= 0) {
            return [];
        }

        if ($this->params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            $entityClass = $this->params->getEntityNamespace() . '\\' . $this->params->getEntityClass();
            $pkField = $this->params->getPkField();

            return $this->entitiesToContao($this->entityManager->getRepository($entityClass)->findOneBy([$pkField => $pk]));
        }
        $modelClass = $this->params->getModelClass();
        if ($modelClass) {
            return $modelClass::findByPk($pk);
        }

        return [];
    }

    /**
     * @param string $fieldname
     * @param string $value
     */
    public function findOneBy($fieldname, $value, $arrOptions = [])
    {
        if ($this->params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            //ToDo arrOptions abbilden

            $entityClass = $this->params->getEntityNamespace() . '\\' . $this->params->getEntityClass();

            return $this->entitiesToContao($this->entityManager->getRepository($entityClass)->findOneBy([$fieldname => $value]));
        }
        $modelClass = $this->params->getModelClass();
        if ($modelClass) {
            return $modelClass::findOneBy($fieldname, $value, $arrOptions);
        }

        return [];
    }

    public function insert($set)
    {
        $result = [];
        if ($this->params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            $entity = $this->contaoToEntity($set);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $result['insertId'] = $entity->getId();

            return $result;
        }
        $tableName = $this->params->getTableName();
        $database = $this->params->getDatabase();
        foreach ($set as $key => $value) {
            if (!$database->fieldExists($key, $tableName)) {
                unset($set[$key]);
            }
        }
        $objInsertStmt = $database->prepare("INSERT INTO $tableName %s")
                ->set($set)
                ->execute();

        if (!$objInsertStmt->affectedRows) {
            return false;
        }

        $result['insertId'] = $objInsertStmt->insertId;

        return $result;
    }

    public function update($id, $set, $id_fieldName = 'id')
    {
        if ($this->params->getType() == C4GBrickDatabaseType::DOCTRINE) {
            $entity = $this->contaoToEntity($set);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $result['insertId'] = $entity->getId();

            return $result;
        }
        $tableName = $this->params->getTableName();
        $database = $this->params->getDatabase();

        if (($id) && ($id_fieldName)) {
            $objInsertStmt = $database->prepare("UPDATE $tableName %s WHERE $id_fieldName=?")
                    ->set($set)
                    ->execute($id);
        }

        if (!$objInsertStmt->affectedRows) {
            return false;
        }

        $result['insertId'] = $objInsertStmt->insertId;

        return $result;
    }

    /**
     * @return EntityManager|null
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return null
     */
    public function getParams()
    {
        return $this->params;
    }
}
