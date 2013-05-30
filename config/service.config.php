<?php

namespace JaztecAcl;

use Zend\Cache\StorageFactory;

return array(
    'invokables' => array(
        'jaztec_acl_service' => 'JaztecAcl\Service\AclService',
    ),
    'factories' => array(
        'jaztec_cache' => function($sm) {
            $config = $sm->get('Config');
            if (array_key_exists('cache', $config['jaztec'])) {
                $storage = StorageFactory::adapterFactory($config['jaztec']['cache']['name']);
            } else {
                $storage = StorageFactory::adapterFactory('FileSystem');
            }
            $plugin = StorageFactory::pluginFactory('serializer', array('serializer' => 'Zend\Serializer\Adapter\PhpSerialize'));
            $storage->addPlugin($plugin);
            return $storage;
        },
        'jaztec_acl' => function($sm) {
//            Cache tijdelijk uitgeschakeld, dit geeft een probleem met Zend\Permissions\Acl\Acl, isAllowed.
//            RoleRegistry verneukt de role wanneer deze opgehaald wordt vanuit een gecached Acl object.
//            $cache = $sm->get('jaztec_cache');
//            if($cache->hasItem('jaztec_acl'))
//                return $cache->getItem('jaztec_acl');
//            else
            return new Acl\Acl();
        }
    ),
    'initializers' => array(
        'jaztec_em' => function($instance, $sm) {
            if ($instance instanceof Service\AbstractDoctrineService) {
                $instance->setEntityManager($sm->get('doctrine.entitymanager.orm_default'));
            }
        },
        'jaztec_acl' => function($instance, $sm) {
            if ($instance instanceof Acl\AclAwareInterface) {
                $instance->setAcl($sm->get('jaztec_acl'));
            }
        },
        'jaztec_cache' => function($instance, $sm) {
            if ($instance instanceof Cache\CacheAwareInterface) {
                $instance->setCacheStorage($sm->get('jaztec_cache'));
            }
        },
    ),
);
