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
        $this->aclService = $this->serviceManager->get('jaztec_acl');
    }

    public function testAclClass()
    {
    }

}
