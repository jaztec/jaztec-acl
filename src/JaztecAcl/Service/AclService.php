<?php

namespace JaztecAcl\Service;

use JaztecAcl\Acl\AclAwareInterface;
use JaztecAcl\Cache\CacheAwareInterface;
use JaztecAcl\Acl\Acl as JaztecAclAcl;
use JaztecAcl\Entity\Monitor\AclRequest;
use Zend\Cache\Storage\StorageInterface;
use JaztecBase\Service\AbstractService;
use JaztecBase\ORM\EntityManagerAwareInterface;
use JaztecBase\ORM\EntityManagerAwareTrait;

class AclService extends AbstractService implements
    AclAwareInterface,
    CacheAwareInterface,
    EntityManagerAwareInterface
{

    use EntityManagerAwareTrait;

    /** @var \JaztecAcl\Acl\Acl $acl */
    protected $acl;

    /** @var \ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth */
    protected $userAuth;

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
            $acl->setupAcl();
            // Refresh the cached instance of the ACL.
            if (!$cache->hasItem('jaztec_acl')) {
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

        $resourceName = $resource;
        if ($resource instanceof \JaztecAcl\Entity\Acl\Resource) {
            $resourceName = $resource->getName();
        }
        // Track requests if set tot true.
        if ($cfg['jaztec_acl']['track_privilege_requests'] === true) {
            $acl->checkPrivilegeRequest($privilege, $resourceName, $this->getEntityManager());
        }

        $allowed = $acl->isAllowed($role, $resource, $privilege);

        // Track requests further if set to true.
        if ($cfg['jaztec_acl']['track_acl_requests'] == true) {
            $request = new AclRequest();
            $request->setAllowed($allowed);
            $request->setDateTime(new \DateTime());
            $request->setRole($role instanceof \JaztecAcl\Entity\Acl\Role ? $role->getRoleId() : $role);
            $request->setResource($resourceName);
            $request->setPrivilege($privilege);
            $this->getEntityManager()->persist($request);
            $this->getEntityManager()->flush($request);
        }
        
        return $allowed;
    }
}
