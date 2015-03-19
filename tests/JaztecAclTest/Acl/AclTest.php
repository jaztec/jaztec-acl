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

        $resource1 = new \JaztecAcl\Entity\Resource('resource01');
        $resource2 = new \JaztecAcl\Entity\Resource('resource02', $resource1);
        $resource3 = new \JaztecAcl\Entity\Resource('resource03', $resource2);
        $resource4 = new \JaztecAcl\Entity\Resource('resource04');
        $resource5 = new \JaztecAcl\Entity\Resource('resource05', $resource4);
        
        $privilege1 = new \JaztecAcl\Entity\Privilege();
        $privilege1->setResource($resource5);
        $privilege1->setRole($em->getRepository('JaztecAcl\Entity\Role')->findBy(['name' => 'guest']));
        $privilege1->setType('allow');
        
        $em->persist($privilege1);
        $em->persist($resource1);
        $em->persist($resource2);
        $em->persist($resource3);
        $em->persist($resource4);
        $em->persist($resource5);
        $em->flush();
        
        // Clear the ACL.
        $this->acl->removeResourceAll();
        $this->acl->removeRoleAll();
        $this->acl->setupAcl();
        
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::isAllowed
     */
    public function testControlList()
    {
        // Testing default capabilities
        $this->acl->allow('guest', 'resource01');
        $this->acl->deny('supermember', 'resource01');
        $this->acl->allow('supermember', 'resource02');

        // Testing for solid control list.
        $this->assertTrue($this->acl->isAllowed('guest', 'resource01'), 'guest should have access to resource01');
        $this->assertTrue($this->acl->isAllowed('guest', 'resource02'), 'guest should have access to resource02');
        $this->assertTrue($this->acl->isAllowed('guest', 'resource03'), 'guest should have access to resource03');
        $this->assertTrue($this->acl->isAllowed('guest', 'resource05'), 'guest should have access to resource05');
        $this->assertFalse($this->acl->isAllowed('supermember', 'resource01'), 'supermember should not have access to resource01');
        $this->assertTrue($this->acl->isAllowed('supermember', 'resource02'), 'supermember should have access to resource02');
        $this->assertTrue($this->acl->isAllowed('supermember', 'resource03'), 'supermember should have access to resource03');
        $this->assertFalse($this->acl->isAllowed('moderator', 'resource01'), 'moderator should not have access to resource01');
        $this->assertTrue($this->acl->isAllowed('moderator', 'resource02'), 'supermember should have access to resource02');
        $this->assertTrue($this->acl->isAllowed('moderator', 'resource03'), 'supermember should have access to resource03');
        $this->assertTrue($this->acl->isAllowed('member', 'resource01'), 'member should have access to resource01');
        $this->assertTrue($this->acl->isAllowed('member', 'resource02'), 'member should have access to resource02');
        $this->assertTrue($this->acl->isAllowed('member', 'resource03'), 'member should have access to resource03');
    }

    /**
     * @covers \JaztecAcl\Acl\Acl::isAllowed
     */
    public function testControlDetail()
    {
        // Testing special capabilities.
        $this->acl->deny();
        $this->acl->allow('supermember');
        $this->acl->allow('guest', 'resource05', 'index');

        // Are is the right role permitted?
        $this->assertFalse($this->acl->isAllowed('guest', 'resource01'), 'The guest role is no longer allowed on this resource');
        $this->assertFalse($this->acl->isAllowed('member', 'resource01'), 'A role derived from the guest role is no longer allowed on this resource');
        $this->assertTrue($this->acl->isAllowed('supermember', 'resource01'), 'The ACL should allow this role because it as all rights.');
        $this->assertTrue($this->acl->isAllowed('guest', 'resource05', 'index'), 'The ACL should allow this role with this privilege because it was directly set.');
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
