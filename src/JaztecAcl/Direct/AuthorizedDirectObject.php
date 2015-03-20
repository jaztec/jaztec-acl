<?php

namespace JaztecAcl\Direct;

use JaztecAcl\Acl\Acl;
use JaztecAcl\Service\AclService;
use Zend\Permissions\Acl\Role\RoleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Service\User as UserService;

class AuthorizedDirectObject implements
    ServiceLocatorAwareInterface
{

    /** @var ZfcUser\Service\User $em */
    protected $userService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /** @var \Zend\Permissions\Acl\Role\RoleInterface $role */
    protected $role;

    /** @var \JaztecAcl\Service\AclService $aclService */
    protected $aclService;

    /** @var string $aclDenominator */
    protected $aclDenominator;

    /**
     * Constructor is needed to setup the aclDenominator. This will be used
     * to check the ACL against.
     *
     * Inherited classes should set their own denominator when initialized.
     */
    public function __construct()
    {
        $this->aclDenominator = 'base/direct';
    }

    /**
     * Checks the ACL registry.
     *
     * @param  string  $privilege
     * @return boolean
     */
    public function checkAcl($privilege)
    {
        // Find the base resource name this module is given.
        $moduleName = substr(get_class($this), 0, strpos(get_class($this), '\\'));
        $config     = $this->getServiceLocator()->get('Config');
        $baseName   = $config['jaztec_acl']['name'][$moduleName];
        $allowed    = $this->getAclService()->isAllowed($this->getRole(), $this->aclDenominator, $privilege, $baseName);

        return $allowed;
    }

    /**
     * Returns a not allowed array
     */
    public function notAllowed()
    {
        return [
            'success' => false,
            'message' => 'not allowed',
        ];
    }

    /**
     * @return \JaztecAcl\Service\AclService
     */
    protected function getAclService()
    {
        if (null === $this->aclService) {
            $this->aclService = $this->getServiceLocator()->get('jaztec_acl_service');
        }

        return $this->aclService;
    }

    /**
     * @param  \JaztecAcl\Service\AclService        $aclService
     * @return \JaztecAcl\Controller\BaseController
     */
    public function setAclService(AclService $aclService)
    {
        $this->aclService = $aclService;

        return $this;
    }

    /**
     * @param  \Zend\Permissions\Acl\Role\RoleInterface         $role
     * @return \JaztecAcl\Direct\AbstractAuthorizedDirectObject
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
            if ($this->getUserService()->getAuthService()->hasIdentity()) {
                $role = $this->getUserService()->getAuthService()->getIdentity()->getRole();
            } else {
                // Setup a guest role.
                $role = new \JaztecAcl\Entity\Acl\Role('guest');
            }
            $this->setRole($role);
        }

        return $this->role;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(\Doctrine\ORM\EntityManager $em)
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
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->locator;
    }

    /**
     * @param  \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \JaztecAcl\Direct\AbstractDirectObject
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->locator = $serviceLocator;

        return $this;
    }

    /**
     * @return \ZfcUser\Service\User
     */
    public function getUserService()
    {
        if (null === $this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }

        return $this->userService;
    }

    /**
     * @param  \ZfcUser\Service\User                      $userService
     * @return \JaztecAclAdmin\Controller\UsersController
     */
    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;

        return $this;
    }
}
