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

    public function indexAction()
    {
        return new ViewModel([
            'loaiSachs' => $this->loaiSachTable->getAll(),
        ]);
    }

    public function addAction()
    {
        return new ViewModel();
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        return new ViewModel([
            'id' => $id,
            'loaiSach' => $this->loaiSachTable->getById($id),
        ]);
    }
}
