<?php

namespace JaztecAclTest\Service;

use JaztecAclTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class AclServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var \JaztecAcl\Service\AclService
     */
    protected $aclService;

    public function setUp()
    {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->aclService = $this->serviceManager->get('jaztec_acl_service');
    }

    public function testAclClass()
    {
        // Testing for a good ACL object.
        $this->assertTrue($this->aclService->getAcl() instanceof \JaztecAcl\Acl\Acl);
    }
}
