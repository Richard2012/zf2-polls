<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Polls\Controller\Polls' => 'Polls\Controller\PollsController',
        ),
    ),

    'router' => array(
        'routes' => array(
            'polls' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/poll[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Polls\Controller\Polls',
                        'action'     => 'display',
                    ),
                ),
            ),
            'poll' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Polls\Controller\Polls',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'poll' => __DIR__ . '/../view',
        ),
        'base_path' => '/labs/polls',
    ),
);