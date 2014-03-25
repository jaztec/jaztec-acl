<?php

namespace JaztecAclTest\Service;

use JaztecAclTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class AclTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \JaztecAcl\Acl\Acl
     */
    protected $acl;

    public function setUp()
    {
        Bootstrap::setUpAclDatabase();

        $em = $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        /* @var $em \Doctrine\ORM\EntityManager */
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->acl            = $this->serviceManager->get('jaztec_acl_service')->getAcl();

        // Clear the ACL.
        $this->acl->removeResourceAll();
        $this->acl->removeRoleAll();
        $this->acl->setupAcl($em);

        // Add a test resource to the ACL
        $this->acl->addResource('resource01');
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::isAllowed
     */
    public function testControlList()
    {
        // Testing default capabilities
        $this->acl->allow('guest', 'resource01');
        $this->acl->deny('additionalRole', 'resource01');

        // Testing for solid control list.
        $this->assertTrue($this->acl->isAllowed('guest', 'resource01'));
        $this->assertFalse($this->acl->isAllowed('additionalRole', 'resource01'));
        $this->assertTrue($this->acl->isAllowed('member', 'resource01'));
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::isAllowed
     */
    public function testControlDetail()
    {
        // Testing special capabilities.
        $this->acl->deny();
        $this->acl->allow('additionalRole');

        // Are is the right role permitted?
        $this->assertFalse($this->acl->isAllowed('guest', 'resource01'));
        $this->assertFalse($this->acl->isAllowed('member', 'resource01'));
        $this->assertTrue($this->acl->isAllowed('additionalRole', 'resource01'), 'The ACL should allow this role because it as all rights.');
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::isLoaded
     */
    public function testLoaded()
    {
        $this->assertTrue($this->acl->isLoaded());
    }

}
