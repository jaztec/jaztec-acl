<?php

namespace JaztecAclTest\Controller;

use JaztecAclTest\Bootstrap;
use JaztecAcl\Controller\AuthorizedController;
use PHPUnit_Framework_TestCase;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Response;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;

class AuthorizedControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \JaztecAcl\Controller\AuthorizedController
     */
    protected $controller;

    /**
     * @var \Zend\Http\Request
     */
    protected $request;

    /**
     * @var \Zend\Http\Response
     */
    protected $response;

    /**
     * @var \Zend\Mvc\Route\RouteMatch
     */
    protected $routeMatch;

    /**
     * @var \Zend\Mvc\MvcEvent
     */
    protected $event;

    protected function setUp()
    {
        // Gather variables
        $serviceManager   = Bootstrap::getServiceManager();
        $this->controller = new AuthorizedController();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array());
        $this->event      = new MvcEvent();
        $config           = $serviceManager->get('Config');
        $routerConfig     = isset($config['router']) ? $config['router'] : array();
        $router           = HttpRouter::factory($routerConfig);

        // Setup the actual testcase.
        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
        
        // Arranging the zfc-user services.
        $authMock = $this->getMock('ZfcUser\Controller\Plugin\ZfcUserAuthentication');
        $this->controller->getPluginManager()
             ->setService('zfcUserAuthentication', $authMock);
    }

    public function testCheckAcl()
    {
        $this->routeMatch->setParam('action', 'index');

        $result   = $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test if the getRole function always returns a RoleInterface of the guest type.
     * 
     * @covers \JaztecAcl\Controller\AuthorizedController::getRole
     */
    public function testGetRole()
    {
        $role = $this->controller->getRole();

        $this->assertInstanceOf('\Zend\Permissions\Acl\Role\RoleInterface', $role);
        $this->assertEquals('guest', $role->getRoleId());
    }

}
