<?php

declare(strict_types=1);

namespace Application;

use Application\Model\LoaiSachTable;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\Db\TableGateway\TableGatewayInterface;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';

        return $config;
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                LoaiSachTable::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);

                    $tableGateway = new TableGateway(
                        'LoaiSach',
                        $dbAdapter
                    );

                    return new LoaiSachTable($tableGateway);
                },
            ],
        ];
    }
}
