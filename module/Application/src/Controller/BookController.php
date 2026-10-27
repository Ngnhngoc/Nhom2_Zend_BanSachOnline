<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\DauSachTable;
use Application\Model\LoaiSachTable;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;

class BookController extends AbstractActionController
{
    public function __construct(
        private DauSachTable $dauSachTable,
        private LoaiSachTable $loaiSachTable
    ) {
    }

    /**
     * Danh sách đầu sách và tìm kiếm dành cho Quản trị viên (Admin).
     */
    public function indexAction(): ViewModel|Response
    {
        // 1. Kiểm tra đăng nhập
        if (! $this->isAuthenticated()) {
            return $this->redirect()->toRoute('home');
        }

        // 2. Chốt phân quyền: Chỉ Admin mới được truy cập
        if (! $this->isAdmin()) {
            $this->flashMessenger()->addErrorMessage('Bạn không có quyền truy cập khu vực Quản trị đầu sách!');

            return $this->redirect()->toRoute('home');
        }

        $keyword = $this->params()->fromQuery('keyword', null);
        $loaiSachIdParam = $this->params()->fromQuery('loai_sach_id', '');
        $loaiSachId = ($loaiSachIdParam !== '' && $loaiSachIdParam !== null)
            ? (int) $loaiSachIdParam
            : null;

        // Tìm kiếm đầu sách (kèm tên loại sách JOIN từ bảng loaisach)
        $dauSachs  = $this->dauSachTable->search($keyword, $loaiSachId);
        $loaiSachs = $this->loaiSachTable->getAll();

        return new ViewModel([
            'dauSachs'           => $dauSachs,
            'loaiSachs'          => $loaiSachs,
            'keyword'            => $keyword ?? '',
            'selectedLoaiSachId' => $loaiSachIdParam,
        ]);
    }

    private function isAuthenticated(): bool
    {
        $session = new Container('user_session');

        return isset($session->user);
    }

    private function isAdmin(): bool
    {
        $session = new Container('user_session');

        return isset($session->user) && ($session->user['role'] ?? '') === 'Admin';
    }
}
