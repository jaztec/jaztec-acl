<?php

namespace JaztecAcl;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use KJSencha\Direct\DirectEvent;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{

    public function init(ModuleManager $moduleManager)
    {
        $events = $moduleManager->getEventManager()->getSharedManager();
        $controllerCallback = array($this, 'onDispatchController');
        $directCallback = array($this, 'onDispatchDirect');
        $events->attach('Zend\Mvc\Application',                 MvcEvent::EVENT_DISPATCH,           $controllerCallback);
        $events->attach('KJSencha\Controller\DirectController', DirectEvent::EVENT_DISPATCH_RPC,    $directCallback);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getServiceConfig()
    {
        return include __DIR__ . '/../../config/service.config.php';
    }

    /**
     * Perform an ACL check when an AutherizedController is dispatched.
     *
     * @param \Zend\Mvc\MvcEvent $e
     */
    public function onDispatchController(MvcEvent $e)
    {
        $controller = $e->getTarget();

        // Check ACL
        if ($controller instanceof \JaztecAcl\Controller\AutherizedController) {
            $controller->checkAcl($e);
        }
    }

    /**
     * Perform an ACL check when a AuthorizedDirectObject is dispatched.
     *
     * @param  \Zend\Mvc\MvcEvent $e
     * @return null|array
     */
    public function onDispatchDirect(Event $e)
    {
        $object = $e->getParam('object');
        $method = $e->getParam('rpc')->getMethod();

        // Check ACL
        if ($object instanceof \JaztecAcl\Direct\AuthorizedDirectObject) {
            if (!$object->checkAcl($method)) {
                $e->stopPropagation(true);

                return $object->notAllowed();
            }
        }
    }

}
