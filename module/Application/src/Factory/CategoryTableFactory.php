<?php

declare(strict_types=1);

namespace Application\Factory;

use Application\Model\CategoryTable;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\TableGateway\TableGateway;
use Psr\Container\ContainerInterface;

class CategoryTableFactory
{
    public function __invoke(ContainerInterface $container): CategoryTable
    {
        $adapter = $container->get(AdapterInterface::class);

        return new CategoryTable(new TableGateway('categories', $adapter));
    }
}
