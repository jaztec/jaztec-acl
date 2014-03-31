<?php

namespace JaztecAcl;

return array(
    'jaztec_acl' => array(
        'name'                             => array(
        // Module base name here, specify once per module.
        ),
        // Auto-create resources when the called resource does not exist.
        'create_resource'                  => true,
        // Track privilege requests.
        'track_privilege_requests'         => true,
        // Redirect the AuthorizedController on Acl failure.
        'redirect_controller'              => true,
        // To which route the AuthorizedController will redirect and additional params.
        'redirect_controller_route'        => 'zfcuser/login',
        'redirect_controller_route_params' => array('redirect' => 'admin'),
    ),
    'zfcuser'    => array(
        'user_entity_class'     => 'JaztecAcl\Entity\User',
        'enable_registration'   => false,
        'enable_username'       => true,
        'enable_display_name'   => true,
        'auth_identity_fields'  => array(
            'username'
        ),
        'login_redirect_route'  => 'jaztecadmin_protected',
        'enable_user_state'     => true,
        'allowed_login_states'  => array(1),
    ),
    'doctrine'   => array(
        'driver' => array(
            'jaztec_driver'  => array(
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
            'orm_default'    => array(
                'drivers' => array(
                    'JaztecAcl\Entity' => 'jaztec_driver',
                    'ZfcUser\Entity'   => 'zfcuser_entity',
                )
            )
        )
    ),
);
