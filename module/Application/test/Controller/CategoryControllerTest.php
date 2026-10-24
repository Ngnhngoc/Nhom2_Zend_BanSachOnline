<?php

declare(strict_types=1);

namespace ApplicationTest\Controller;

use Application\Model\LoaiSachTable;
use Laminas\Session\Container;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

final class CategoryControllerTest extends AbstractHttpControllerTestCase
{
    private LoaiSachTable $loaiSachTable;

    public function setUp(): void
    {
        $configOverrides = [
            'module_listener_options' => [
                'config_cache_enabled'     => false,
                'module_map_cache_enabled' => false,
            ],
        ];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();

        $this->loaiSachTable = $this->createMock(LoaiSachTable::class);
        $services = $this->getApplicationServiceLocator();
        $services->setAllowOverride(true);
        $services->setService(LoaiSachTable::class, $this->loaiSachTable);
        $services->setAllowOverride(false);

        $session = new Container('user_session');
        $session->user = [
            'id'       => 1,
            'username' => 'testuser',
            'role'     => 'NhanVien',
            'ho_ten'   => 'Người kiểm thử',
        ];
    }

    protected function tearDown(): void
    {
        $session = new Container('user_session');
        unset($session->user);

        parent::tearDown();
    }

    public function testAddRejectsDuplicateNameAndKeepsSubmittedData(): void
    {
        $this->loaiSachTable
            ->expects(self::once())
            ->method('nameExists')
            ->with('Văn học', null)
            ->willReturn(true);
        $this->loaiSachTable
            ->expects(self::never())
            ->method('save');

        $this->dispatch('/category/add', 'POST', [
            'ten_loai'   => '  Văn   học  ',
            'mo_ta'      => 'Loại sách trùng tên',
            'trang_thai' => '1',
        ]);

        $this->assertResponseStatusCode(200);
        $content = (string) $this->getResponse()->getContent();
        self::assertStringContainsString('Tên loại sách đã tồn tại.', $content);
        self::assertStringContainsString(
            'value="Văn học"',
            html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
    }

    public function testAddSavesValidCategoryAndRedirects(): void
    {
        $this->loaiSachTable
            ->method('nameExists')
            ->willReturn(false);
        $this->loaiSachTable
            ->expects(self::once())
            ->method('save')
            ->with([
                'ten_loai'   => 'Khoa học',
                'mo_ta'      => 'Sách khoa học',
                'trang_thai' => 1,
            ]);

        $this->dispatch('/category/add', 'POST', [
            'ten_loai'   => 'Khoa học',
            'mo_ta'      => 'Sách khoa học',
            'trang_thai' => '1',
        ]);

        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/category');
    }

    public function testAddRejectsInvalidInput(): void
    {
        $this->loaiSachTable
            ->expects(self::never())
            ->method('nameExists');
        $this->loaiSachTable
            ->expects(self::never())
            ->method('save');

        $this->dispatch('/category/add', 'POST', [
            'ten_loai'   => '---',
            'mo_ta'      => str_repeat('a', 1001),
            'trang_thai' => '7',
        ]);

        $this->assertResponseStatusCode(200);
        $content = (string) $this->getResponse()->getContent();
        self::assertStringContainsString('Tên loại sách phải có ít nhất một chữ cái hoặc chữ số.', $content);
        self::assertStringContainsString('Mô tả không được dài quá 1000 ký tự.', $content);
        self::assertStringContainsString('Trạng thái loại sách không hợp lệ.', $content);
    }

    public function testEditSavesValidChangesAndRedirects(): void
    {
        $this->loaiSachTable
            ->method('getById')
            ->with(1)
            ->willReturn([
                'id'         => 1,
                'ten_loai'   => 'Tên cũ',
                'mo_ta'      => 'Mô tả cũ',
                'trang_thai' => 1,
            ]);
        $this->loaiSachTable
            ->expects(self::once())
            ->method('nameExists')
            ->with('Tên mới', 1)
            ->willReturn(false);
        $this->loaiSachTable
            ->expects(self::once())
            ->method('save')
            ->with([
                'id'         => 1,
                'ten_loai'   => 'Tên mới',
                'mo_ta'      => 'Mô tả mới',
                'trang_thai' => 0,
            ]);

        $this->dispatch('/category/edit/1', 'POST', [
            'ten_loai'   => 'Tên mới',
            'mo_ta'      => 'Mô tả mới',
            'trang_thai' => '0',
        ]);

        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/category');
    }

    public function testEditRedirectsWhenCategoryDoesNotExist(): void
    {
        $this->loaiSachTable
            ->expects(self::once())
            ->method('getById')
            ->with(999)
            ->willReturn(null);

        $this->dispatch('/category/edit/999', 'GET');

        $this->assertResponseStatusCode(302);
        $this->assertRedirectTo('/category');
    }
}
