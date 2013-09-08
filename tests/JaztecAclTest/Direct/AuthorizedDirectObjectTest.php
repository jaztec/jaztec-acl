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

    public function setUp() {
        $this->directObject = new \JaztecAcl\Direct\AuthorizedDirectObject();
        $this->directObject->setServiceLocator(Bootstrap::getServiceManager());
    }

    /**
     * @covers \JaztecAcl\Direct\AuthorizedDirectObject::notAllowed
     */
    public function testNotAllowedResponse() {
        $response = $this->directObject->notAllowed();

        $this->assertTrue(is_array($response));
        $this->assertFalse($response['success']);
        $this->assertEquals('not allowed', $response['message']);
    }
}
