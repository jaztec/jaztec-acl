<?php

namespace Jaztec;

use Zend\ModuleManager\ModuleManager;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

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