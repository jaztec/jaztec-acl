<?php

namespace JaztecAcl;

return array(
    'jaztec_acl'    => array(
        'name'  => array(
            // Module base name here, specify once per module.
        ),
        // Auto-create resources when the called resource does not exist.
        'create_resource'                   => true,
        // Redirect the AutherizedController on Acl failure.
        'redirect_controller'               => true,
        // To which route the AutherizedController will redirect and additional params.
        'redirect_controller_route'         => 'zfcuser/login',
        'redirect_controller_route_params'  => array('redirect' => 'admin'),
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
