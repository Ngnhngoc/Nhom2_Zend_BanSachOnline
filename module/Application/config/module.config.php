<?php

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Application\Model\LoaiSachTable;
use Application\Model\NguoiDungTable;
use Application\Model\DauSachTable;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type'    => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        // BẮT BUỘC SỬA DÒNG NÀY THÀNH AuthController
                        'controller' => Controller\AuthController::class, 
                        
                        // Action trỏ về hàm loginAction
                        'action'     => 'login', 
                    ],
                ],
            ],
            // Khai báo đường dẫn Quản lý loại sách
            'category' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/category[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\CategoryController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            // Khai báo đường dẫn Quản lý đầu sách
            'book' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/book[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\BookController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            // Khai báo đường dẫn Đăng nhập
            'auth' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/auth[/:action]',
                    'defaults' => [
                        'controller' => Controller\AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],


   
'service_manager' => [
    'factories' => [
        Model\NguoiDungTable::class => function ($container) {
            return new Model\NguoiDungTable(
                $container->get('NguoiDungTableGateway')
            );
        },

        'NguoiDungTableGateway' => function ($container) {
    $dbAdapter = $container->get(
        \Laminas\Db\Adapter\AdapterInterface::class
    );

    return new \Laminas\Db\TableGateway\TableGateway(
        'nguoidung',
        $dbAdapter
    );
},

        
Model\DauSachTable::class => function ($container) {
    return new Model\DauSachTable(
        $container->get('DauSachTableGateway')
    );
},

'DauSachTableGateway' => function ($container) {
    $dbAdapter = $container->get(
        \Laminas\Db\Adapter\AdapterInterface::class
    );

    return new \Laminas\Db\TableGateway\TableGateway(
        'dausach',
        $dbAdapter
    );
},


        Model\LoaiSachTable::class => function ($container) {
            return new Model\LoaiSachTable(
                $container->get('LoaiSachTableGateway')
            );
        },

        'LoaiSachTableGateway' => function ($container) {
    $dbAdapter = $container->get(
        \Laminas\Db\Adapter\AdapterInterface::class
    );

    return new \Laminas\Db\TableGateway\TableGateway(
        'loaisach',
        $dbAdapter
    );
},
    ],
],


    'controllers' => [
    'factories' => [
        Controller\IndexController::class => InvokableFactory::class,

        Controller\CategoryController::class => function ($container) {
            return new Controller\CategoryController(
                $container->get(Model\LoaiSachTable::class)
            );
        },

        Controller\BookController::class => function ($container) {
            return new Controller\BookController(
                $container->get(Model\DauSachTable::class),
                $container->get(Model\LoaiSachTable::class)
            );
        },

        Controller\AuthController::class => function ($container) {
            return new Controller\AuthController(
                $container->get(Model\NguoiDungTable::class)
            );
        },
    ],
],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];