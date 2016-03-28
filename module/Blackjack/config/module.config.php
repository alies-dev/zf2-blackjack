<?php

namespace Blackjack;

use Blackjack\Entity\Hand;
use Blackjack\Manager\GameManager;
use Blackjack\Manager\HandManager;
use Blackjack\Manager\UserManager;
use Blackjack\Service\ShufflingMachine;

return array(
    'service_manager' => array(
        'factories' => array(
            'Blackjack\Manager\UserManager' => function ($sm) {
                $em = $sm->get('Doctrine\ORM\EntityManager');
                $userRepository = $em->getRepository('Blackjack\Entity\User');

                return new UserManager($em, $userRepository);
            },
            'Blackjack\Manager\GameManager' => function ($sm) {
                $em = $sm->get('Doctrine\ORM\EntityManager');
                $gameRepository = $em->getRepository('Blackjack\Entity\Game');
                $userManager = $sm->get('Blackjack\Manager\UserManager');
                $shufflingMachine = new ShufflingMachine();

                return new GameManager(
                    $em,
                    $gameRepository,
                    $userManager,
                    $shufflingMachine
                );
            },
        ),
    ),
    'router'          => array(
        'routes' => array(
            'home' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Blackjack\Controller\Default',
                        'action'     => 'index',
                    ),
                ),
            ),
            'apiGame'  => array(
                'type'    => 'literal',
                'options' => array(
                    'route'       => '/api/game',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'Blackjack\Controller',
                        'controller'    => 'Blackjack\Controller\Game',
                        'action'        => 'previous',
                    ),
                ),
                'child_routes' => array(
                    'doAction' => array(
                        'type' => 'segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/:id/:action',
                            'verb' => 'post,put',
                            'constraints' => array(
                                'gameId' => '[0-9]*/?',
                                'action' => '[a-zA-Z]*/?',
                            ),
                        ),
                    ),
                    'getGame' => array(
                        'type' => 'segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/:action',
                            'verb' => 'get',
                            'constraints' => array(
                                'action' => '[a-zA-Z]*/?',
                            ),
                        ),
                    ),
                )
            ),
            'apiUser'  => array(
                'type'    => 'literal',
                'options' => array(
                    'route'       => '/api/user',
                    'defaults'    => array(
                        '__NAMESPACE__' => 'Blackjack\Controller',
                        'controller'    => 'Blackjack\Controller\User',
                        'action'        => 'scores',
                    ),
                ),
                'child_routes' => array(
                    'getScores' => array(
                        'type' => 'segment',
                        'may_terminate' => true,
                        'options' => array(
                            'route' => '/scores',
                            'verb' => 'get',
                        ),
                    ),
                )
            ),
        ),
    ),
    'controllers'     => array(
        'invokables' => array(
            'Blackjack\Controller\Default' => 'Blackjack\Controller\DefaultController',
            'Blackjack\Controller\Game'    => 'Blackjack\Controller\GameController',
            'Blackjack\Controller\User'    => 'Blackjack\Controller\UserController',
        ),
    ),
    'view_manager'    => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'strategies'               => array(
            'ViewJsonStrategy',
        ),
        'template_map'             => array(
            'blackjack/default/index' => __DIR__
                .'/../view/blackjack/index/index.phtml',
            'error/404'               => __DIR__.'/../view/error/404.phtml',
            'error/index'             => __DIR__.'/../view/error/index.phtml',
        ),
        'template_path_stack'      => array(
            'blackjack' => __DIR__.'/../view',
        ),
    ),
    'doctrine'        => array(
        'driver' => array(
            __NAMESPACE__.'_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(__DIR__.'/../src/'.__NAMESPACE__.'/Entity'),
            ),
            'orm_default'           => array(
                'drivers' => array(
                    __NAMESPACE__.'\Entity' => __NAMESPACE__.'_entity',
                ),
            ),
        ),
    ),
    // Placeholder for console routes
    'console'         => array(
        'router' => array(
            'routes' => array(),
        ),
    ),
);