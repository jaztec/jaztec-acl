<?php

namespace JaztecAcl;

use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use KJSencha\Direct\DirectEvent;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;

/**
 * Module class for integration of JaztecAcl into the ZF2 framework.
 */
class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    ConsoleUsageProviderInterface
{

    public function init(ModuleManager $moduleManager)
    {
        $eventvents         = $moduleManager->getEventManager()->getSharedManager();
        $controllerCallback = [$this, 'onDispatchController'];
        $directCallback     = [$this, 'onDispatchDirect'];
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
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ]
            ]
        ];
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
     * @param \Zend\Mvc\MvcEvent $event
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

    /**
     * Get help output for this module console actions.
     * 
     * @param \Zend\Console\Adapter\AdapterInterface $console
     * @return type
     */
    public function getConsoleUsage(Console $console)
    {
        return [
            'acl database <clean-install|update> [--email=] [--help|-h] '
            . '[--verbose|-v]' => 'Perform database actions for this module',
            [
                'clean-install',
                'Perform a clean install on the database from the ACL objects'
            ],
            [
                'update',
                'Update the database with any changes to the ACL objects'
            ],
            ['[--email]', 'E-mail for an admin user to be added'],
            ['[--help|-h]','Display help information'],
            ['[--verbose|-v]', 'Display console output'],
        ];
    }
}
