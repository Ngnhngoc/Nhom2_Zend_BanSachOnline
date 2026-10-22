<?php

declare(strict_types=1);

namespace ApplicationTest\Controller;

use Application\Controller\CategoryController;
use Application\Model\CategoryRepositoryInterface;
use ApplicationTest\Double\InMemoryCategoryRepository;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

final class CategoryControllerTest extends AbstractHttpControllerTestCase
{
    private InMemoryCategoryRepository $repository;

    public function setUp(): void
    {
        $this->repository = new InMemoryCategoryRepository([
            [
                'id'            => 1,
                'category_code' => 'L01',
                'category_name' => 'Công nghệ thông tin',
                'status'        => 1,
            ],
        ]);

        $configOverrides = [
            'module_listener_options' => [
                'config_cache_enabled' => false,
                'module_map_cache_enabled' => false,
            ],
        ];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $services = $this->getApplicationServiceLocator();
        $services->setAllowOverride(true);
        $services->setService(CategoryRepositoryInterface::class, $this->repository);
        $services->setAllowOverride(false);
    }

    public function testIndexDisplaysCategoriesFromRepository(): void
    {
        $this->dispatch('/category', 'GET');

        $this->assertResponseStatusCode(200);
        $this->assertControllerName(CategoryController::class);
        $content = (string) $this->getResponse()->getContent();
        $this->assertStringContainsString('L01', $content);
        $this->assertStringContainsString('Công nghệ thông tin', $content);
    }

    public function testAddRejectsDuplicateCodeAndKeepsSubmittedData(): void
    {
        $this->dispatch('/category/add', 'POST', [
            'category_code' => 'l01',
            'category_name' => 'Sách mới',
            'status'        => '1',
        ]);

        $this->assertResponseStatusCode(200);
        $this->assertCount(1, $this->repository->fetchAll());
        $content = (string) $this->getResponse()->getContent();
        $this->assertStringContainsString('Mã loại sách đã tồn tại.', $content);
        $this->assertStringContainsString('value="L01"', $content);
    }

    public function testAddCreatesCategoryAndRedirectsToList(): void
    {
        $this->dispatch('/category/add', 'POST', [
            'category_code' => 'l02',
            'category_name' => '  Văn học  nghệ thuật  ',
            'status'        => '0',
        ]);

        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/category');

        $created = $this->repository->find(2);
        self::assertNotNull($created);
        self::assertSame('L02', $created['category_code']);
        self::assertSame('Văn học nghệ thuật', $created['category_name']);
        self::assertSame(0, $created['status']);
    }

    public function testEditUpdatesExistingCategory(): void
    {
        $this->dispatch('/category/edit/1', 'POST', [
            'category_code' => 'l01',
            'category_name' => 'Khoa học máy tính',
            'status'        => '0',
        ]);

        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/category');

        $updated = $this->repository->find(1);
        self::assertNotNull($updated);
        self::assertSame('Khoa học máy tính', $updated['category_name']);
        self::assertSame(0, $updated['status']);
    }

    public function testEditRedirectsWhenCategoryDoesNotExist(): void
    {
        $this->dispatch('/category/edit/999', 'GET');

        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/category');
    }
}
