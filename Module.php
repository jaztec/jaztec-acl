<?php

namespace Jaztec;

use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use KJSencha\Direct\DirectEvent;
use KJSencha\Controller\DirectController;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function init(ModuleManager $moduleManager) 
    { 
        $events = $moduleManager->getEventManager()->getSharedManager(); 
        $events->attach(
            'Zend\Mvc\Application', 
            'dispatch', 
            function($e) {
                $controller = $e->getTarget();
                
                // Acl controle uitvoeren
                if($controller instanceof \Jaztec\Controller\AutherizedController) {
                    $controller->checkAcl($e);
                }
            }
        ); 
        $events->attach(
            'KJSencha\Controller\DirectController', 
            DirectEvent::EVENT_DISPATCH_RPC, 
            function($e) {
                $object = $e->getParam('object');
                
                // Acl controle uitvoeren
                if($object instanceof \Jaztec\Direct\AbstractAuthorizedDirectObject) {
                    if(!$object->checkAcl()) {
                        $e->stopPropagation(true);
                        return $object->notAllowed();
                    }
                }
            }
        ); 
    } 

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig() {
        return include __DIR__ . '/config/service.config.php';
    }
}