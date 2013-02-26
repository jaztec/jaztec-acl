<?php

namespace Jaztec\Controller;

use Jaztec\Entity\Role;
use Doctrine\ORM\EntityManager;

class AutherizedController extends BaseController
{
    /** @var EntityManager $em */
    protected $em;

    /** @var Role $role */
    protected $role;
       
    /**
     * @param Role $role
     * @return \Jaztec\Controller\AutherizedController 
     */
    public function setRole(Role $role) {
        $this->role = $role;
        return $this;
    }
    
    /**
     * @return Role
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