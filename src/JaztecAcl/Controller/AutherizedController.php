<?php

namespace JaztecAcl\Controller;

use JaztecAcl\Entity\Role;
use JaztecAcl\Service\AclService;
use JaztecAcl\Service\AclServiceAwareInterface;
use Doctrine\ORM\EntityManager;
use Zend\Permissions\Acl\Role\RoleInterface;

class AutherizedController extends BaseController implements
    AclServiceAwareInterface
{
    /** @var EntityManager $em */
    protected $em;

    /** @var \Zend\Permissions\Acl\Role\RoleInterface $role */
    protected $role;

    /** @var JaztecAcl\Service\AclService $aclService */
    protected $aclService;

    /**
     * @param  \Zend\Permissions\Acl\Role\RoleInterface   $role
     * @return \JaztecAcl\Controller\AutherizedController
     */
    public function setRole(RoleInterface $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getRole()
    {
        if (null === $this->role) {
            if ($this->zfcUserAuthentication()->hasIdentity()) {
                $role = $this->zfcUserAuthentication()->getIdentity()->getRole();
            } else {
                $em = $this->getEntityManager();
                /** @var EntityManager $em */
                // Haal de eerste rol op, altijd guest.
                $role = $em->find('\JaztecAcl\Entity\Role', 1);
            }
            $this->setRole($role);
        }

        return $this->role;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }

    /**
     * @return \JaztecAcl\Service\AclService
     */
    public function getAclService()
    {
        if (null === $this->aclService) {
            $this->aclService = $this->getServiceLocator()->get('jaztec_acl_service');
        }

        return $this->aclService;
    }

    /**
     * @param \JaztecAcl\Service\AclService $aclService
     */
    public function setAclService(AclService $aclService)
    {
        $this->aclService = $aclService;
    }

    /**
     * @param  \Zend\Mvc\MvcEvent $e
     * @return boolean
     */
    public function checkAcl(\Zend\Mvc\MvcEvent $e)
    {
        $params = $e->getRouteMatch()->getParams();

        // Finding the module name in which the controller is declared.
        $moduleName = substr(get_class($this), 0, strpos(get_class($this), '\\'));
        $config = $this->getServiceLocator()->get('Config');
        $baseName = $config['jaztec_acl']['name'][$moduleName];
var_dump($moduleName . ' ' . $baseName);
        $allowed = $this->getAclService()->isAllowed(
            $this->getRole(), $params['controller'], $params['action'], $baseName
        );

        // Redirect the user if this is specified in the configuration.
        if (!$allowed) {
            if ($config['jaztec_acl']['redirect_controller'] == true) {
                $this->redirect()->toRoute($config['jaztec_acl']['redirect_controller_route'], $config['jaztec_acl']['redirect_controller_route_params']);
            }
        }

        return $allowed;
    }

}
