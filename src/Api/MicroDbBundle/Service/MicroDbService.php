<?php

namespace Api\MicroDbBundle\Service;

use Api\MicroDbBundle\Interfaces\ObjectInterface;
use MicroDB\Database;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class MicroDbService
 */
class MicroDbService
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var Database
     */
    protected $db;

    /**
     * @var string
     */
    protected $dbDir;

    /**
     * @param array $vars
     *
     * @return array
     */
    protected function filterArray(array $vars)
    {
        $fields = [
            'container',
            'db',
            'dbDir',
        ];

        foreach ($fields as $field) {
            unset($vars[$field]);
        }

        return $vars;
    }

    /**
     * @param array $vars
     * @param string $class
     *
     * @return ObjectInterface
     */
    protected function toObject(array $vars, $class)
    {
        $object = new $class();

        foreach ($vars as $field => $value) {
            $object->$field = $value;
        }

        return $object;
    }

    /**
     * @param ObjectInterface $object
     *
     * @return ObjectInterface
     */
    public function save(ObjectInterface $object)
    {
        $vars = get_object_vars($object);

        if (empty($vars['id'])) {
            // Is new
            $vars['id'] = md5(uniqid(rand(), true));
        } else {
            // Find by id
            $vars = $this->getDb()->load($vars['id']);
        }

        if (empty($vars)) {
            return;
        }

        // Filter array
        $vars = $this->filterArray($vars);

        // Save
        $this->getDb()->save($vars['id'], $vars);

        return $object = $this->toObject($vars, $this->getObjectClass());
    }

    /**
     * @param array $params
     *
     * @return array|null
     */
    public function find(array $params = [])
    {
        $objects = $this->getDb()->find();

        if (!empty($params)) {
            foreach ($objects as $object) {
                $persist = false;
                foreach ($params as $key => $value) {
                    if (empty($object[$key]) || $object[$key] != $value) {
                        break;
                    }
                    $persist = true;
                }

                if ($persist) {
                    $storage[] = $object;
                }
            }

            $objects = null;

            if (!empty($storage)) {
                $objects = $storage;
            }
        }

        $result = [];

        if (!empty($objects)) {
            foreach ($objects as $object) {
                $result[] = $this->toObject($object, $this->getObjectClass());
            }
        }

        return $result;
    }

    /**
     * @param array $params
     *
     * @return null|ObjectInterface
     */
    public function findOne(array $params)
    {
        $result = null;

        $objects = $this->find($params);

        if (!empty($objects)) {
            $result = reset($objects);
        }

        return $result;
    }

    /**
     * @param ObjectInterface $entity
     *
     * @return array
     */
    public function delete(ObjectInterface $entity)
    {
        return $this->getDb()->delete($entity->getId());
    }

    /**
     * @param ObjectInterface $entity
     *
     * @return array
     */
    protected function toArray(ObjectInterface $entity)
    {
        return get_object_vars($entity);
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     *
     * @return MicroDbService $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @return Database
     */
    public function getDb()
    {
        return $this->db = new \MicroDB\Database($this->container->getParameter('database_dir').'/core');
    }

    /**
     * @return string
     */
    public function getDbDir()
    {
        return $this->dbDir;
    }

    /**
     * @param string $dbDir
     *
     * @return MicroDbService $this
     */
    public function setDbDir($dbDir)
    {
        $this->dbDir = $dbDir;

        return $this;
    }
}
