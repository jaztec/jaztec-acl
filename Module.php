<?php

namespace Jaztec;

use Zend\Http\Request,
    Zend\ModuleManager\ModuleManager,
    Doctrine\ORM\EntityManager,
    Jaztec\Entity\User,
    Jaztec\Acl\Acl;

class Module
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

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }
}