<?php

declare(strict_types=1);

namespace Application\Factory;

use Application\Controller\CategoryController;
use Application\Model\CategoryRepositoryInterface;
use Psr\Container\ContainerInterface;

class CategoryControllerFactory
{
    public function __invoke(ContainerInterface $container): CategoryController
    {
        return new CategoryController($container->get(CategoryRepositoryInterface::class));
    }
}
