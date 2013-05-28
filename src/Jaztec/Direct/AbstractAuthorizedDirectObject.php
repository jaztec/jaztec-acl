<?php

namespace Jaztec\Direct;

use Jaztec\Acl\Acl;
use Jaztec\Service\AclService;
use Zend\Permissions\Acl\Role\RoleInterface;
 
class AbstractAuthorizedDirectObject extends AbstractDirectObject
{
    /** @var \Zend\Permissions\Acl\Role\RoleInterface $role */
    protected $role;
    
    /** @var \Jaztec\Service\AclService $aclService */
    protected $aclService;

    /** @var string $aclDenominator */
    protected $aclDenominator;
    
    /**
     * Checks the ACL registry.
     * @return boolean
     */
    public function checkAcl()
    {
        var_dump($this->aclDenominator); 
        $allowed = $this->getAclService()->isAllowed($this->getRole(), $this->aclDenominator, '');
        return $allowed;
    }
    
    /**
     * Returns a not allowed array
     */
    public function notAllowed()
    {
        return array(
            'success'   => false,
            'message'   => 'not allowed',
        );
    }
    
    /**
     * @return \Jaztec\Service\AclService
     */
    protected function getAclService()
    {
        if (null === $this->aclService) {
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
    
    /**
     * @param \Zend\Permissions\Acl\Role\RoleInterface $role
     * @return \Jaztec\Direct\AbstractAuthorizedDirectObject
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
            if ($this->getUserService()->getAuthService()->hasIdentity()) { 
                $role = $this->getUserService()->getAuthService()->getIdentity()->getRole();
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
}