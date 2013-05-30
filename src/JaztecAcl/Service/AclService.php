<?php

namespace JaztecAcl\Service;

use JaztecAcl\Acl\AclAwareInterface;
use JaztecAcl\Cache\CacheAwareInterface;
use JaztecAcl\Acl\Acl as JaztecAclAcl;
use Zend\Cache\Storage\StorageInterface;

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

    /** @var array */
    protected $config;

    /**
     * @param \Zend\Cache\Storage\StorageInterface $storage
     */
    public function setCacheStorage(StorageInterface $storage) {
        $this->cacheStorage = $storage;
    }

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCacheStorage() {
        return $this->cacheStorage;
    }

    /**
     * @return @\JaztecAcl\Acl\Acl 
     */
    public function getAcl() {
        return $this->acl;
    }

    /**
     * @param \JaztecAcl\Acl\Acl $acl
     * @return \JaztecAcl\Acl\Acl 
     */
    public function setAcl(JaztecAclAcl $acl) {
        $this->acl = $acl;
    }

    /**
     * @return \ZfcUser\Controller\Plugin\ZfcUserAuthentication
     */
    public function getUserAuth() {
        return $this->userAuth;
    }

    /**
     * @param \ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth
     * @return \JaztecAcl\Acl\Acl 
     */
    public function setUserAuth(\ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth) {
        $this->userAuth = $userAuth;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if (null === $this->em) {
            $this->setEntityManager($this->getServiceLocator()->get('doctrine.entitymanager.orm_default'));
        }
        return $this->em;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em 
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @param Zend\Acl\Role\RoleInterface|string $role
     * @param Zend\Acl\Role\RoleInterface|string $resource
     * @param string $privilege
     * 
     * @return bool
     */
    public function isAllowed($role, $resource, $privilege) {
        $acl = $this->getAcl();

        if (!$acl->isLoaded()) {
//            $cache = $this->getCacheStorage();
            $acl->setupAcl($this->getEntityManager());
//            if($cache->hasItem('jaztec_acl'))
//                $cache->removeItem('jaztec_acl');
//            $cache->addItem('jaztec_acl', $acl); 
        }

        // Check resource existance.
        if (!$acl->hasResource($resource)) {
            return false;
        }

        return $acl->isAllowed($role, $resource, $privilege);
    }

}