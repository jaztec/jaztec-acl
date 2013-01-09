<?php

namespace Jaztec\Service;

class AclService extends AbstractService
{
        /** @var \Jaztec\Acl\Acl $acl */
    protected $acl;
    
    /** @var \ZfcUser\Controller\Plugin\ZfcUserAuthentication $userAuth */
    protected $userAuth;
    
    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;
    
    /**
     * @return @\Jaztec\Acl\Acl 
     */
    public function getAcl() {
        if(null === $this->acl) {
            $this->acl = new JaztecAcl($this->getEntityManager());
        }
        
        return @$this->acl;
    }

    /**
     * @param \Jaztec\Acl\Acl $acl
     * @return \Jaztec\Acl\Acl 
     */
    public function setAcl(JaztecAcl $acl) {
        $this->acl = $acl;
        
        return $this;
    }

    /**
     * @return ZfcUserAuthentication
     */
    public function getUserAuth() {
        return $this->userAuth;
    }

    /**
     * @param ZfcUserAuthentication $userAuth
     * @return \Jaztec\Acl\Acl 
     */
    public function setUserAuth(ZfcUserAuthentication $userAuth) {
        $this->userAuth = $userAuth;
        return $this;
    }
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if(null === $this->em) {
            $this->em = $this->getServiceManager()->get('doctrine.entitymanager.orm_default');
        }
        
        return $this->em;
    }
    
    /**
     * @param \Doctrine\ORM\EntityManager $em 
     */
    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }
    
    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager->getServiceLocator();
    }

    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
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
        
        if(!$acl->isLoaded()) {
            $acl->setupAcl();
        }
        
        // Controleer of de resource bestaat
        if(!$acl->hasResource($resource)) {
            return false;
        }

        return $acl->isAllowed($role, $resource, $privilege);
    }
}