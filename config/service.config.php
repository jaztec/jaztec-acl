<?php

namespace Jaztec;

return array(
    
    'invokables' => array(
        'jaztec_acl_service'   => 'Jaztec\Service\AclService',
    ),

    'factories' => array(
        
    ),
    
    'initializers' => array(
        'jaztec_em' => function($instance, $sm) {
            if ($instance instanceof Service\AbstractDoctrineService) {
                $instance->setEntityManager($sm->get('doctrine.entitymanager.orm_default'));
            }
        },
    ),
);
