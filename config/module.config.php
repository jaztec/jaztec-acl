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
        // Default cache configuration.
        'cache'                            => array(
            'adapter'	=> array(
                'name'      => 'memory',
                'options'   => array(),
            ),
            'plugins'   => array(
                'exception_handler' => array('throw_exceptions' => true),
                'serializer'
            )
        ),
        // Use cache.
        'use_cache'                        => true,
        /*
         * The following is setup data for a clean database install.
         * If you have installed the database manually you can get default
         * role information here.
         */
        'setUp' => array(
            'roles' => array(
                array(
                    'name' => 'guest',
                    'sort' => 0
                ),
                array(
                    'name'   => 'registered',
                    'parent' => 'guest',
                    'sort'   => 1
                ),
                array(
                    'name'   => 'member',
                    'parent' => 'registered',
                    'sort'   => 2
                ),
                array(
                    'name'   => 'supermember',
                    'parent' => 'member',
                    'sort'   => 3
                ),
                array(
                    'name'   => 'moderator',
                    'parent' => 'supermember',
                    'sort'   => 4
                ),
                array(
                    'name'   => 'admin',
                    'parent' => 'moderator',
                    'sort'   => 5
                ),
            ),
        ),
    ),
    /**
     * Controller configuration fot the ConsoleController.
     */
    'controllers'     => array(
        'invokables' => array(
            'jaztecacl/console' => 'JaztecAcl\Controller\ConsoleController',
        ),
    ),
    /**
     * Route options for console functionality
     */
    'console'    => array(
        'router'    => array(
            'routes' => array(
                'update-database' => array(
                    'options' => array(
                        'route'     => 'acl database [clean-install|update] [--email=] [--help|-h] [--verbose|-v]',
                        'defaults'  => array(
                            'controller'    => 'jaztecacl/console',
                            'action'        => 'update-database',
                        ),
                    ),
                ),
            ),
        )
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
