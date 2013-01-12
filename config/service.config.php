<?php

namespace Jaztec;

use Zend\Cache\StorageFactory;

return array(
    
    'invokables' => array(
        'jaztec_acl_service'   => 'Jaztec\Service\AclService',
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
        }
    ),
    
    'initializers' => array(
        'jaztec_em' => function($instance, $sm) {
            if ($instance instanceof Service\AbstractDoctrineService) {
                $instance->setEntityManager($sm->get('doctrine.entitymanager.orm_default'));
            }
        },
        'jaztec_cache' => function($instance, $sm) {
            if( $instance instanceof Cache\CacheAwareInterface ) {
                $instance->setCacheStorage($sm->get('jaztec_cache'));
            }
        },
    ),
);
