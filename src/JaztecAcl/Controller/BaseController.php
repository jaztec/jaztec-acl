<?php

namespace JaztecAcl\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use ZfcUser\Service\User as UserService;
use Zend\Mvc\MvcEvent;

class BaseController extends AbstractActionController
{

    /** @var EntityManager $em */
    protected $em;

    /** @var ZfcUser\Service\User $em */
    protected $userService;

    /**
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatch(MvcEvent $e)
    {
        parent::onDispatch($e);
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
