<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\DauSachTable;
use Application\Model\LoaiSachTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function __construct(
        private DauSachTable $dauSachTable,
        private LoaiSachTable $loaiSachTable
    ) {
    }

    /**
     * Giao diện trang chủ xem, tìm kiếm và lọc sách dành cho Khách hàng.
     */
    public function indexAction(): ViewModel
    {
        $keyword = $this->params()->fromQuery('keyword', null);
        $loaiSachIdParam = $this->params()->fromQuery('loai_sach_id', '');
        $loaiSachId = ($loaiSachIdParam !== '' && $loaiSachIdParam !== null)
            ? (int) $loaiSachIdParam
            : null;

        // Chỉ lấy các loại sách đang hoạt động (trang_thai = 1) để hiển thị bộ lọc
        $loaiSachs = $this->loaiSachTable->search(null, 1);

        // Tìm kiếm đầu sách theo từ khóa và loại sách
        $dauSachs = $this->dauSachTable->search($keyword, $loaiSachId);

        return new ViewModel([
            'dauSachs'           => $dauSachs,
            'loaiSachs'          => $loaiSachs,
            'keyword'            => $keyword ?? '',
            'selectedLoaiSachId' => $loaiSachIdParam,
        ]);
    }
}
