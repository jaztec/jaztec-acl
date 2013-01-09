<?php

namespace Jaztec\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcUser\Service\User as UserService;
use Jaztec\Service\AclService;
use Zend\Mvc\MvcEvent;

class BaseController extends AbstractActionController
{
    /** @var EntityManager $em */
    protected $em;
    
    /** @var ZfcUser\Service\User $em */
    protected $userService;
    
    /** @var Jaztec\Service\AclService $aclService */
    protected $aclService;
    
    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e) {
        date_default_timezone_set('Europe/Amsterdam');
        
        parent::onDispatch($e);
    }    
    
    /**
     * @param EntityManager $em 
     */
    public function setEntityManager(EntityManager $em) {
        $this->em = $em;
    }
    
    /**
     * @return EntityManager 
     */
    public function getEntityManager() {
        if(null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }
    
    /**
     * @return \ZfcUser\Service\User
     */
    public function getUserService()
    {
        if (!$this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }
        return $this->userService;
    }

    /**
     * @param \ZfcUser\Service\User $userService
     * @return \JaztecAdmin\Controller\UsersController
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
        return $this;
    }
    
    /**
     * @return \Jaztec\Service\AclService
     */
    public function getAclService()
    {
        if (!$this->aclService) {
            $this->aclService = $this->getServiceLocator()->get('jaztec_acl_service');
        }
        return $this->aclService;
    }
    
    /**
     * @param \Jaztec\Service\AclService $aclService
     * @return \Jaztec\Controller\BaseController
     */
    public function setAclService(AclService $aclService)
    {
        $this->aclService = $aclService;
        return $this;
    }
}