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

        $em                   = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
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
        $this->assertFalse($this->acl->isAllowed('guest', 'resource01'), 'The guest role is no longer allowed on this resource');
        $this->assertFalse($this->acl->isAllowed('member', 'resource01'), 'A role derived from the guest role is no longer allowed on this resource');
        $this->assertTrue($this->acl->isAllowed('additionalRole', 'resource01'), 'The ACL should allow this role because it as all rights.');
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::isLoaded
     */
    public function testLoaded()
    {
        $this->assertTrue($this->acl->isLoaded());
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::createResource
     */
    public function testCreateResource()
    {
        // Set up ACL
        $this->acl->allow();

        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        /* @var $em \Doctrine\ORM\EntityManager */

        $this->acl->createResource('resource2', 'base', $em);

        $resource = $em->getRepository('JaztecAcl\Entity\Resource')->findOneBy(array(
            'name' => 'resource2'
        ));
        /* @var $resource \JaztecAcl\Entity\Resource */

        // Test if the resource exists.
        $this->assertTrue(($resource instanceof \JaztecAcl\Entity\Resource), "A new resource should've been added");
        $this->assertTrue($this->acl->hasResource($resource), "The local ACL should contain the newly added resource");
    }

    public function testCreatePrivilegeRequest()
    {
        // Set up the ACL
        $this->acl->allow();

        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        /* @var $em \Doctrine\ORM\EntityManager */

        $this->acl->checkPrivilegeRequest('index', 'resource01', $em);

        $requests = $em->getRepository('JaztecAcl\Entity\RequestedPrivilege')->findBy(
            array(
                'privilege' => 'index',
                'resource'  => 'resource01',
            )
        );
        /* @var $requests array */
        $this->assertGreaterThan(0, count($requests), "The newly added privilege should exist in the database");
        $this->assertEquals(1, count($requests), "At least 1 occurance of this requested privilege should occur in the database");

        $this->acl->checkPrivilegeRequest('index', 'resource01', $em);

        $requestsNewRun = $em->getRepository('JaztecAcl\Entity\RequestedPrivilege')->findBy(
            array(
                'privilege' => 'index',
                'resource'  => 'resource01',
            )
        );
        /* @var $requestsNewRun array */
        $this->assertGreaterThan(0, count($requestsNewRun), "The newly added privilege should exist in the database, also in the second run");
        $this->assertEquals(1, count($requestsNewRun), "After a second run their thould only be one instance of the requested privilege");
    }
}
