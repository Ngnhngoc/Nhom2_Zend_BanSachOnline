<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\LoaiSachTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

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
}
