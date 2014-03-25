<?php

return array(
    'TestSuite' => array(
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
                array(
                    'name'   => 'additionalRole',
                    'sort'   => 6
                ),
            ),
        ),
    ),
);
