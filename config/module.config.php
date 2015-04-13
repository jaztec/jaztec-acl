<?php

namespace JaztecAcl;

return [
    'jaztec_acl' => [
        'name'                             => [
        // Module base name here, specify once per module.
        ],
        // Auto-create resources when the called resource does not exist.
        'create_resource'                  => true,
        // Track privilege requests.
        'track_privilege_requests'         => true,
        // Used to track all requests made to the ACL module with timestamp and the result of the request.
        'track_acl_requests'               => true,
        // Redirect the AuthorizedController on Acl failure.
        'redirect_controller'              => true,
        // To which route the AuthorizedController will redirect and additional params.
        'redirect_controller_route'        => 'zfcuser/login',
        'redirect_controller_route_params' => ['redirect' => 'admin'],
        // Default cache configuration.
        'cache'                            => [
            'adapter'	=> [
                'name'      => 'memory',
                'options'   => [],
            ],
            'plugins'   => [
                'exception_handler' => ['throw_exceptions' => true],
                'serializer'
            ]
        ],
        // Use cache.
        'use_cache'                        => true,
        /*
         * The following is setup data for a clean database install.
         * If you have installed the database manually you can get default
         * role information here.
         */
        'setUp' => [
            'roles' => [
                [
                    'name' => 'guest',
                    'sort' => 0
                ],
                [
                    'name'   => 'registered',
                    'parent' => 'guest',
                    'sort'   => 1
                ],
                [
                    'name'   => 'member',
                    'parent' => 'registered',
                    'sort'   => 2
                ],
                [
                    'name'   => 'supermember',
                    'parent' => 'member',
                    'sort'   => 3
                ],
                [
                    'name'   => 'moderator',
                    'parent' => 'supermember',
                    'sort'   => 4
                ],
                [
                    'name'   => 'admin',
                    'parent' => 'moderator',
                    'sort'   => 5
                ],
            ],
        ],
    ],
    /**
     * Controller configuration fot the ConsoleController.
     */
    'controllers'     => [
        'invokables' => [
            'jaztecacl/console' => 'JaztecAcl\Controller\ConsoleController',
        ],
    ],
    /**
     * Route options for console functionality
     */
    'console'    => [
        'router'    => [
            'routes' => [
                'update-database' => [
                    'options' => [
                        'route'     => 'acl database [clean-install|update] [--email=] [--help|-h] [--verbose|-v]',
                        'defaults'  => [
                            'controller'    => 'jaztecacl/console',
                            'action'        => 'update-database',
                        ]
                    ]
                ],
                'add-user' => [
                    'options' => [
                        'route' => 'acl new-user --email=|-e --password=|-p [--help|-h] [--verbose|-v]',
                        'defaults' => [
                            'controller' => 'jaztecacl/console',
                            'action' => 'new-user'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'zfcuser'    => [
        'user_entity_class'     => 'JaztecAcl\Entity\Auth\User',
        'enable_registration'   => false,
        'enable_username'       => true,
        'enable_display_name'   => true,
        'auth_identity_fields'  => [
            'username'
        ],
        'login_redirect_route'  => 'home',
        'enable_user_state'     => true,
        'allowed_login_states'  => [1],
        'tableName'             => 'AclUsers'
    ],
    'doctrine'   => [
        'driver' => [
            'jaztec_driver'  => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/JaztecAcl/Entity',
                ]
            ],
            'zfcuser_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/JaztecAcl/Entity',
                ]
            ],
            'orm_default'    => [
                'drivers' => [
                    'JaztecAcl\Entity' => 'jaztec_driver',
                    'ZfcUser\Entity'   => 'zfcuser_entity',
                ]
            ]
        ]
    ],
];
