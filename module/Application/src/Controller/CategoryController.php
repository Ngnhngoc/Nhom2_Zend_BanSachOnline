<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\LoaiSachTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\Session\Container; // Thêm thư viện Session

class CategoryController extends AbstractActionController
{
    private LoaiSachTable $loaiSachTable;

    public function __construct(LoaiSachTable $loaiSachTable)
    {
        $this->loaiSachTable = $loaiSachTable;
    }

    /**
     * Danh sách loại sách và tìm kiếm
     */
    public function indexAction()
    {
        // --- CHỐT CHẶN BẢO VỆ: KIỂM TRA ĐĂNG NHẬP ---
        $session = new Container('user_session');
        if (!isset($session->user)) {
            // Nếu chưa đăng nhập, đá văng về trang chủ (Form Login)
            return $this->redirect()->toRoute('home');
        }
        // ---------------------------------------------

        $keyword = $this->params()->fromQuery('keyword', null);
        $trangThaiParam = $this->params()->fromQuery('trang_thai', '');

        $trangThai = ($trangThaiParam !== '' && $trangThaiParam !== null)
            ? (int) $trangThaiParam
            : null;

        // Xử lý tìm kiếm nếu có từ khóa hoặc trạng thái
        if ($keyword !== null || $trangThai !== null) {
            $loaiSachs = $this->loaiSachTable->search($keyword, $trangThai);
        } else {
            $loaiSachs = $this->loaiSachTable->getAll();
        }

        return new ViewModel([
            'loaiSachs'  => $loaiSachs,
            'keyword'    => $keyword ?? '',
            'trangThai'  => $trangThaiParam,
        ]);
    }
    
    public function deleteAction()
    {
        // --- PHẦN CỦA TÀI: KHÓA BẢO VỆ ---
        $session = new Container('user_session');
        if (!isset($session->user) || $session->user['role'] !== 'Admin') {
            // Nếu là Nhân viên -> Báo lỗi và đẩy về trang danh sách
            $this->flashMessenger()->addErrorMessage('Bạn không có quyền Xóa loại sách!');
            return $this->redirect()->toRoute('category'); 
        }
        
        // ... (Code xử lý xóa dữ liệu của nhóm sẽ nằm ở dưới đây) ...
    }
}