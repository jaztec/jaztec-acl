<?php

namespace JaztecAclTest\Service;

use JaztecAclTest\Bootstrap;
use JaztecAcl\Controller\AuthorizedController;
use PHPUnit_Framework_TestCase;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Response;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

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

    public function setUp() {
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->aclService = $this->serviceManager->get('jaztec_acl');
    }

    public function testAclClass() {
        
    }

}
