<?php

namespace Jaztec\Controller;

use Jaztec\Entity\Role;
use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\Role\RoleInterface;

class AutherizedController extends BaseController
{
    /** @var EntityManager $em */
    protected $em;

    /** @var \Zend\Permissions\Acl\Role\RoleInterface $role */
    protected $role;
       
    /**
     * @param \Zend\Permissions\Acl\Role\RoleInterface $role
     * @return \Jaztec\Controller\AutherizedController 
     */
    public function setRole(RoleInterface $role) {
        $this->role = $role;
        return $this;
    }
    
    /**
     * @return \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getRole() {
        if(null === $this->role) {
            if ($this->zfcUserAuthentication()->hasIdentity()) { 
                $role = $this->zfcUserAuthentication()->getIdentity()->getRole();
            } else {
                $em = $this->getEntityManager();
                /** @var EntityManager $em */
                // Haal de eerste rol op, altijd guest.
                $role = $em->find('\Jaztec\Entity\Role',1);
            }
            $this->setRole($role);
        }
        return $this->role;
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
     * @param MvcEvent $e 
     * @return void
     */
    public function checkAcl(\Zend\Mvc\MvcEvent $e) {
        $params = $e->getRouteMatch()->getParams();
        
        $allowed = $this->getAclService()->isAllowed(
            $this->getRole(),
            $params['controller'],
            $params['action']
        );
        
        // Wanneer de persoon niet toegestaan is wordt deze omgeleid
        if(!$allowed) {
            $this->redirect()->toRoute('zfcuser/login',array('redirect' => 'admin'));
        }
    }

}