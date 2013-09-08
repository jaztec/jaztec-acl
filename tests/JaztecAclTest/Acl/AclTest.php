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
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->acl = $this->serviceManager->get('jaztec_acl_service')->getAcl();
    }

    public function testControlList() {
        // Setup the ACL
        $this->acl->addResource('resource01');
        $this->acl->addRole('role01');
        $this->acl->addRole('role02');
        $this->acl->addRole('role03', 'role01');
        $this->acl->allow('role01', 'resource01');
        $this->acl->deny('role02', 'resource01');
        // Testing for solid control list.
        $this->assertTrue($this->acl->isAllowed('role01', 'resource01'));
        $this->assertFalse($this->acl->isAllowed('role02', 'resource01'));
        $this->assertTrue($this->acl->isAllowed('role03', 'resource01'));
    }
}
