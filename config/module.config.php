<?php

namespace JaztecAcl;

return array(
    'jaztec' => array(
        'cache' => array(
            'name' => 'apc'
        ),
    ),
    'doctrine' => array(
        'driver' => array(
            'jaztec_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/JaztecAcl/Entity',
                )
            ),
            'zfcuser_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/JaztecAcl/Entity',
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    'JaztecAcl\Entity' => 'jaztec_driver',
                    'ZfcUser\Entity' => 'zfcuser_entity',
                )
            )
        )
    ),
);
