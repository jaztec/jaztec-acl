<?php

namespace JaztecAcl\Direct;

use JaztecAcl\Acl\Acl;
use JaztecAcl\Service\AclService;
use Zend\Permissions\Acl\Role\RoleInterface;

class AbstractAuthorizedDirectObject extends AbstractDirectObject {

    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /** @var \Zend\Permissions\Acl\Role\RoleInterface $role */
    protected $role;

    /** @var \JaztecAcl\Service\AclService $aclService */
    protected $aclService;

    /** @var string $aclDenominator */
    protected $aclDenominator;

    /**
     * Checks the ACL registry.
     * @return boolean
     */
    public function checkAcl() {
        $allowed = $this->getAclService()->isAllowed($this->getRole(), $this->aclDenominator, '');
        return $allowed;
    }

    /**
     * Returns a not allowed array
     */
    public function notAllowed() {
        return array(
            'success' => false,
            'message' => 'not allowed',
        );
    }

    /**
     * @return \JaztecAcl\Service\AclService
     */
    protected function getAclService() {
        if (null === $this->aclService) {
            $this->aclService = $this->getServiceLocator()->get('jaztec_acl_service');
        }
        return $this->aclService;
    }

    /**
     * @param \JaztecAcl\Service\AclService $aclService
     * @return \JaztecAcl\Controller\BaseController
     */
    public function setAclService(AclService $aclService) {
        $this->aclService = $aclService;
        return $this;
    }

    /**
     * @param \Zend\Permissions\Acl\Role\RoleInterface $role
     * @return \JaztecAcl\Direct\AbstractAuthorizedDirectObject
     */
    public function setRole(RoleInterface $role) {
        $this->role = $role;
        return $this;
    }

    /**
     * @return \Zend\Permissions\Acl\Role\RoleInterface
     */
    public function getRole() {
        if (null === $this->role) {
            if ($this->getUserService()->getAuthService()->hasIdentity()) {
                $role = $this->getUserService()->getAuthService()->getIdentity()->getRole();
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
    public function setEntityManager(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }

    /**
     * @return \Doctrine\ORM\EntityManager 
     */
    public function getEntityManager() {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

}