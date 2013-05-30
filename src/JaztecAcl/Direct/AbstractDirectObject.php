<?php

namespace JaztecAcl\Direct;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcUser\Service\User as UserService;

class AbstractDirectObject implements
ServiceLocatorAwareInterface {

    /** @var ZfcUser\Service\User $em */
    protected $userService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $locator;

    /**
     * Constructor
     */
    public function __construct() {
        
    }

    /**
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->locator;
    }

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \JaztecAcl\Direct\AbstractDirectObject
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator) {
        $this->locator = $serviceLocator;
        return $this;
    }

    /**
     * @return \ZfcUser\Service\User
     */
    public function getUserService() {
        if (null === $this->userService) {
            $this->userService = $this->getServiceLocator()->get('zfcuser_user_service');
        }
        return $this->userService;
    }

    /**
     * @param \ZfcUser\Service\User $userService
     * @return \JaztecAclAdmin\Controller\UsersController
     */
    public function setUserService(UserService $userService) {
        $this->userService = $userService;
        return $this;
    }

}