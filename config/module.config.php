<?php

namespace Jaztec;

return array( 
    'jaztec' => array(
        'cache' => array(
            'name'      => 'filesystem',
            'options'   => array(
                'cachedir'          => __DIR__ . '/../../../data/cache/acl', 
                'ttl'               => 0,
                'dir_permission'    => 0760,
                'file_permission'   => 0660,
            ),
        ),
    ),
    
    'service_manager' => array( 

    ),
    
    'doctrine' => array(
        'driver' => array(
            'jaztec_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Jaztec/Entity',
                )
            ),
            
            'zfcuser_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/Jaztec/Entity',
                )
            ),
            
            'orm_default' => array(
                'drivers' => array(
                    'Jaztec\Entity'           => 'jaztec_driver',
                    'ZfcUser\Entity'          => 'zfcuser_entity',
                )
            )
        )
    ),
);
