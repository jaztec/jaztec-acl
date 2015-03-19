<?php

namespace JaztecAcl;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

return [
    'invokables' => [
        'jaztec_acl_service' => 'JaztecAcl\Service\AclService',
    ],
    'factories'  => [
        'jaztec_cache' => function(ServiceLocatorInterface $sm) {
            /* @var $config array */
            $config = $sm->get('Config');
            return StorageFactory::factory($config['jaztec_acl']['cache']);
        },
        'jaztec_acl' => function(ServiceLocatorInterface $sm) {
            $cache = $sm->get('jaztec_cache');
            if ($cache->hasItem('jaztec_acl') && $config['jaztec_acl']['use_cache'] === true) {
                $acl = $cache->getItem('jaztec_acl');
            } else {
                $acl = new Acl\Acl();
            }
            $acl->setEntityManager($sm->get('doctrine.entitymanager.orm_default'));
            return $acl;
        }
    ],
    'initializers' => [
        'jaztec_acl' => function($instance, ServiceLocatorInterface $sm) {
            if ($instance instanceof Acl\AclAwareInterface) {
                $instance->setAcl($sm->get('jaztec_acl'));
            }
        },
        'jaztec_aclservice' => function($instance, ServiceLocatorInterface $sm) {
            if ($instance instanceof Service\AclServiceAwareInterface) {
                $instance->setAclService($sm->get('jaztec_acl_service'));
            }
        },
        'jaztec_cache' => function($instance, ServiceLocatorInterface $sm) {
            if ($instance instanceof Cache\CacheAwareInterface) {
                $instance->setCacheStorage($sm->get('jaztec_cache'));
            }
        },
    ],
];
