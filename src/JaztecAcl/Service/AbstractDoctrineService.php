<?php

namespace JAztec\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

/**
 * Doctrine Mapper
 *
 * Provides common doctrine methods
 */
abstract class AbstractDoctrineService extends AbstractService
{
    const TYPE_SERIALIZEDARRAY = 0x1;
    const TYPE_ENTITYARRAY = 0x2;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getDatabase()
    {
        return $this->entityManager->getConnection();
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->entityName);
    }

    /**
     * @param  array|\Doctrine\Common\Persistence\ObjectRepository $repo
     * @param  int                                                 $type
     * @return array
     */
    protected function processResult($repo, $type)
    {
        switch ($type) {
            case AbstractDoctrineMapper::TYPE_SERIALIZEDARRAY:
                $result = array();
                foreach ($repo as $obj) {
                    /* @var $obj \JaztecBase\Entity\EntityInterface */
                    if ($obj instanceof \JaztecBase\Entity\EntityInterface)
                        $result[] = $obj->toArray();
                }
                break;
            case AbstractDoctrineMapper::TYPE_ENTITYARRAY:
                if (!is_array($repo))
                    $repo = array($repo);
                $result = $repo;
                break;
        }

        return $result;
    }

}
