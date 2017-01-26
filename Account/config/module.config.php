<?php

use Mmd\Account\Controller\DashboardController;
use Mmd\Account\Controller\Factory\ProfileControllerFactory;
use Mmd\Account\Controller\ProfileController;
use Mmd\Account\Form\ChangePasswordForm;
use Mmd\Account\Form\Factory\ChangePasswordFormFactory;
use Mmd\Account\Form\Factory\ProfileBaseFormFactory;
use Mmd\Account\Form\ProfileBaseForm;
use Mmd\Account\InputFilter\ChangePasswordFilter;
use Mmd\Account\InputFilter\ProfileBaseFilter;
use Mmd\Account\Service\Factory\SocialAttachmentServiceFactory;
use Mmd\Account\Service\Factory\UserFeedServiceFactory;
use Mmd\Account\Service\SocialAttachmentService;
use Mmd\Account\Service\UserFeedService;
use Mmd\Account\Validator\Factory\PasswordIsMatchFactory;
use Mmd\Account\Validator\Factory\Social\ProfileIsAvailableFactory;
use Mmd\Account\Validator\Factory\Social\ProfileValidatorFactory;
use Mmd\Account\Validator\PasswordIsMatch;
use Mmd\Account\Validator\Social\ProfileIsAvailable;
use Mmd\Account\Validator\Social\ProfileValidator;
use Mmd\Account\View\Factory\Helper\BindSocialProviderFactory;
use Mmd\Account\View\Factory\Helper\UserFeedFactory;
use Mmd\Account\View\Helper\BindSocialProvider;
use Mmd\Account\View\Helper\UserFeed;

return [
    'router'          => [
        'routes' => [
            'dashboard' => [
                'type'    => 'Literal',
                'options' => [
                    'route'       => '/dashboard',
                    'defaults'    => [
                        'controller' => DashboardController::class,
                        'action'     => 'index',
                    ],
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                ],
            ],
            'profile'   => [
                'type'          => 'Literal',
                'options'       => [
                    'route'    => '/profile',
                    'defaults' => [
                        'controller' => ProfileController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'update-base'     => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/update-base',
                            'defaults' => [
                                'action' => 'update-base',
                            ],
                        ],
                    ],
                    'change-password' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/change-password',
                            'defaults' => [
                                'action' => 'change-password',
                            ],
                        ],
                    ],
                    'social'          => [
                        'type'          => 'Segment',
                        'options'       => [
                            'route'       => '/social/:provider',
                            'constraints' => [
                                'provider' => '[\w]+',
                            ],
                        ],
                        'may_terminate' => false,
                        'child_routes'  => [
                            'attach' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/attach',
                                    'defaults' => [
                                        'action' => 'attach-social',
                                    ],
                                ],
                            ],
                            'detach' => [
                                'type'    => 'Literal',
                                'options' => [
                                    'route'    => '/detach',
                                    'defaults' => [
                                        'action' => 'detach-social',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers'     => [
        'invokables' => [
            DashboardController::class => DashboardController::class,
        ],
        'factories'  => [
            ProfileController::class => ProfileControllerFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'controller_map'      => [
            DashboardController::class => 'mmd-account/dashboard',
        ],
    ],
    'form_elements'   => [
        'factories' => [
            ProfileBaseForm::class    => ProfileBaseFormFactory::class,
            ChangePasswordForm::class => ChangePasswordFormFactory::class,
        ],
    ],
    'input_filters'   => [
        'invokables' => [
            ProfileBaseFilter::class    => ProfileBaseFilter::class,
            ChangePasswordFilter::class => ChangePasswordFilter::class,
        ],
    ],
    'validators'      => [
        'factories' => [
            PasswordIsMatch::class    => PasswordIsMatchFactory::class,
            ProfileIsAvailable::class => ProfileIsAvailableFactory::class,
            ProfileValidator::class   => ProfileValidatorFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            UserFeedService::class         => UserFeedServiceFactory::class,
            SocialAttachmentService::class => SocialAttachmentServiceFactory::class,
        ],
    ],
    'view_helpers'    => [
        'aliases'   => [
            'userFeed'              => UserFeed::class,
            'attachSocialProviders' => BindSocialProvider::class,
        ],
        'factories' => [
            UserFeed::class           => UserFeedFactory::class,
            BindSocialProvider::class => BindSocialProviderFactory::class,
        ],
    ],
    'navigation'      => [
        'default' => [
            'account' => [
                'label' => 'Аккаунт',
                'route' => 'dashboard',
                'class' => 'sa-side-page',
                'pages' => [
                    [
                        'label' => 'Стартовая панель',
                        'route' => 'dashboard',
                    ],
                    [
                        'label'  => 'Мои Персонажи',
                        'route'  => 'character/manage',
                        'params' => [
                            'action' => 'list',
                        ],
                    ],
                    [
                        'label'  => 'Мои Гильдии',
                        'route'  => 'guild/manage',
                        'params' => [
                            'action' => 'list',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
