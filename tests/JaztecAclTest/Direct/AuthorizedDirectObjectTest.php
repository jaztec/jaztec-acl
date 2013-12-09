<?php

namespace JaztecAclTest\Controller;

use JaztecAclTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class AuthorizedDirectObjectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \JaztecAcl\Direct\AuthorizedDirectObject
     */
    protected $directObject;

    public function setUp()
    {
        $this->directObject = new \JaztecAcl\Direct\AuthorizedDirectObject();
        $this->directObject->setServiceLocator(Bootstrap::getServiceManager());
    }

    /**
     * @covers \JaztecAcl\Direct\AuthorizedDirectObject::notAllowed
     */
    public function testNotAllowedResponse()
    {
        $response = $this->directObject->notAllowed();

        $this->assertTrue(is_array($response));
        $this->assertFalse($response['success']);
        $this->assertEquals('not allowed', $response['message']);
    }

    /**
     * Test if the getRole function always returns a RoleInterface of the guest type.
     * 
     * @covers \JaztecAcl\Direct\AuthorizedDirectObject::getRole
     */
    public function testGetRole()
    {
        $role = $this->directObject->getRole();

        $this->assertInstanceOf('\Zend\Permissions\Acl\Role\RoleInterface', $role);
        $this->assertEquals('guest', $role->getRoleId());
    }
}
