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
        $eventvents = $moduleManager->getEventManager()->getSharedManager();
        $controllerCallback = array($this, 'onDispatchController');
        $directCallback = array($this, 'onDispatchDirect');
        $eventvents->attach('Zend\Mvc\Application', MvcEvent::EVENT_DISPATCH, $controllerCallback);
        $eventvents->attach('KJSencha\Controller\DirectController', DirectEvent::EVENT_DISPATCH_RPC, $directCallback);
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
     * Perform an ACL check when an AuthorizedController is dispatched.
     *
     * @param \Zend\Mvc\MvcEvent $eventvent
     */
    public function onDispatchController(MvcEvent $eventvent)
    {
        $controller = $eventvent->getTarget();

        // Check ACL
        if ($controller instanceof \JaztecAcl\Controller\AuthorizedController) {
            $controller->checkAcl($eventvent);
        }
    }

    /**
     * Perform an ACL check when a AuthorizedDirectObject is dispatched.
     *
     * @param  \Zend\Mvc\MvcEvent $event
     * @return null|array
     */
    public function onDispatchDirect(Event $event)
    {
        $object = $event->getParam('object');
        $method = $event->getParam('rpc')->getMethod();

        // Check ACL
        if ($object instanceof \JaztecAcl\Direct\AuthorizedDirectObject) {
            if (!$object->checkAcl($method)) {
                $event->stopPropagation(true);

                return $object->notAllowed();
            }
        }
    }
}
