<?php

namespace JaztecAcl\Service;

use JaztecAcl\Acl\AclAwareInterface;
use JaztecAcl\Cache\CacheAwareInterface;
use JaztecAcl\Acl\Acl as JaztecAclAcl;
use Zend\Cache\Storage\StorageInterface;
use JaztecBase\Service\AbstractService;

class AclService extends AbstractService implements
    AclAwareInterface,
    CacheAwareInterface
{

    /** @var \JaztecAcl\Acl\Acl $acl */
    protected $acl;

    /** @var \ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth */
    protected $userAuth;

    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /** @var \Zend\Cache\Storage\StorageInterface */
    protected $cacheStorage;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $storage
     */
    public function setCacheStorage(StorageInterface $storage)
    {
        $this->cacheStorage = $storage;
    }

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCacheStorage()
    {
        return $this->cacheStorage;
    }

    /**
     * @return \JaztecAcl\Acl\Acl
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param \JaztecAcl\Acl\Acl $acl
     */
    public function setAcl(JaztecAclAcl $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return \ZfcUser\Controller\Plugin\ZfcUserAuthentication
     */
    public function getUserAuth()
    {
        return $this->userAuth;
    }

    /**
     * @param  \ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth
     * @return \JaztecAcl\Acl\Acl
     */
    public function setUserAuth(\ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth)
    {
        $this->userAuth = $userAuth;

        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }

        return $this->em;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Zend\Acl\Role\RoleInterface|string $role
     * @param Zend\Acl\Role\RoleInterface|string $resource
     * @param string                             $privilege
     * @param Zend\Acl\Role\RoleInterface|string $baseResource This resource is only used when the
     *      requested resource is not known in the ACL.
     *
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege, $baseResource = 'base')
    {
        $acl = $this->getAcl();
        $cfg = $this->getServiceLocator()->get('Config');

        if (!$acl->isLoaded()) {
            $cache = $this->getCacheStorage();
            $acl->setupAcl($this->getEntityManager());
            // Refresh the cached instance of the ACL.
            if(!$cache->hasItem('jaztec_acl')) {
                $cache->removeItem('jaztec_acl');
            }
            $cache->addItem('jaztec_acl', $acl);
        }
        // Check resource existence and create it if the config allows this, by defaultm use 'base'.
        if (!$acl->hasResource($resource)) {
            if (!array_key_exists('create_resource', $cfg['jaztec_acl']) ||
                $cfg['jaztec_acl']['create_resource'] == true) {
                $resource = $acl->createResource($resource, $baseResource, $this->getEntityManager());
            } else {
                return false;
            }
        }

        // Track requests if set tot true.
        if ($cfg['jaztec_acl']['track_privilege_requests'] === true) {
            $resourceName = $resource;
            if ($resource instanceof \JaztecAcl\Entity\Resource) {
                $resourceName = $resource->getName();
            }
            $acl->checkPrivilegeRequest($privilege, $resourceName, $this->getEntityManager());
        }

        return $acl->isAllowed($role, $resource, $privilege);
    }
}
