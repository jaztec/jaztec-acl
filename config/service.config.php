<?php

namespace Jaztec;

use Zend\Cache\StorageFactory;

return array( 
    'invokables' => array(
        'jaztec_acl_service'    => 'Jaztec\Service\AclService',
    ),
    
    'factories' => array(
        'jaztec_cache' => function($sm) {
            $config = $sm->get('Config');
            $storage = StorageFactory::factory(array(
                'adapter'   => $config['jaztec']['cache'],
                'plugins'   => array(
                    array(
                        'name'      => 'serializer',
                        'options'   => array(
                            'serializer' => 'Zend\Serializer\Adapter\PhpCode'
                        ),
                    ),
                ),
            ));
            return $storage;
        },
        'jaztec_acl' => function($sm) {
            $cache = $sm->get('jaztec_cache');
            if($cache->hasItem('jaztec_acl'))
                return unserialize ($cache->getItem('jaztec_acl'));
            else
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
            if( $instance instanceof Acl\AclAwareInterface) {
                $instance->setAcl($sm->get('jaztec_acl'));
            }
        },
        'jaztec_cache' => function($instance, $sm) {
            if( $instance instanceof Cache\CacheAwareInterface ) {
                $instance->setCacheStorage($sm->get('jaztec_cache'));
            }
        },
    ),
);
