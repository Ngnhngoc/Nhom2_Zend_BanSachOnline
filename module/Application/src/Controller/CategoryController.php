<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\LoaiSachTable;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\Container;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;

class CategoryController extends AbstractActionController
{
    public function __construct(private LoaiSachTable $loaiSachTable)
    {
    }

    /**
     * Danh sách loại sách và tìm kiếm.
     */
    public function indexAction(): ViewModel|Response
    {
        if (! $this->isAuthenticated()) {
            return $this->redirect()->toRoute('home');
        }

        $keyword       = $this->params()->fromQuery('keyword', null);
        $trangThaiParam = $this->params()->fromQuery('trang_thai', '');
        $trangThai     = ($trangThaiParam !== '' && $trangThaiParam !== null)
            ? (int) $trangThaiParam
            : null;

        if ($keyword !== null || $trangThai !== null) {
            $loaiSachs = $this->loaiSachTable->search($keyword, $trangThai);
        } else {
            $loaiSachs = $this->loaiSachTable->getAll();
        }

        return new ViewModel([
            'loaiSachs' => $loaiSachs,
            'keyword'   => $keyword ?? '',
            'trangThai' => $trangThaiParam,
        ]);
    }

    /**
     * Thêm loại sách.
     */
    public function addAction(): ViewModel|Response
    {
        if (! $this->isAuthenticated()) {
            return $this->redirect()->toRoute('home');
        }

        $formData = $this->emptyFormData();
        $errors   = [];

        if ($this->getRequest()->isPost()) {
            $formData = $this->readFormData();
            $errors   = $this->validateForm($formData);

            if ($errors === []) {
                try {
                    $this->loaiSachTable->save($formData);
                    $this->flashMessenger()->addSuccessMessage('Thêm loại sách thành công.');

                    return $this->redirect()->toRoute('category');
                } catch (RuntimeException $exception) {
                    $errors['form'] = $exception->getMessage();
                } catch (Throwable) {
                    $errors['form'] = 'Không thể lưu loại sách. Vui lòng thử lại.';
                }
            }
        }

        return new ViewModel([
            'formData' => $formData,
            'errors'   => $errors,
        ]);
    }

    /**
     * Sửa loại sách.
     */
    public function editAction(): ViewModel|Response
    {
        if (! $this->isAuthenticated()) {
            return $this->redirect()->toRoute('home');
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if ($id < 1) {
            $this->flashMessenger()->addErrorMessage('Mã loại sách không hợp lệ.');

            return $this->redirect()->toRoute('category');
        }

        $loaiSach = $this->loaiSachTable->getById($id);
        if ($loaiSach === null) {
            $this->flashMessenger()->addErrorMessage('Không tìm thấy loại sách cần sửa.');

            return $this->redirect()->toRoute('category');
        }

        $formData = [
            'ten_loai'   => (string) $loaiSach['ten_loai'],
            'mo_ta'      => (string) ($loaiSach['mo_ta'] ?? ''),
            'trang_thai' => (int) $loaiSach['trang_thai'],
        ];
        $errors = [];

        if ($this->getRequest()->isPost()) {
            $formData = $this->readFormData();
            $errors   = $this->validateForm($formData, $id);

            if ($errors === []) {
                try {
                    $this->loaiSachTable->save(['id' => $id] + $formData);
                    $this->flashMessenger()->addSuccessMessage('Cập nhật loại sách thành công.');

                    return $this->redirect()->toRoute('category');
                } catch (RuntimeException $exception) {
                    $errors['form'] = $exception->getMessage();
                } catch (Throwable) {
                    $errors['form'] = 'Không thể cập nhật loại sách. Vui lòng thử lại.';
                }
            }
        }

        return new ViewModel([
            'id'       => $id,
            'formData' => $formData,
            'errors'   => $errors,
        ]);
    }

    public function deleteAction()
    {
        // Phần xóa do Duy phụ trách; giữ nguyên chốt phân quyền hiện có.
        $session = new Container('user_session');
        if (! isset($session->user) || $session->user['role'] !== 'Admin') {
            $this->flashMessenger()->addErrorMessage('Bạn không có quyền Xóa loại sách!');

            return $this->redirect()->toRoute('category');
        }

        return $this->redirect()->toRoute('category');
    }

    /** @return array{ten_loai: string, mo_ta: string, trang_thai: int} */
    private function emptyFormData(): array
    {
        return [
            'ten_loai'   => '',
            'mo_ta'      => '',
            'trang_thai' => 1,
        ];
    }

    /** @return array{ten_loai: string, mo_ta: string, trang_thai: int} */
    private function readFormData(): array
    {
        $name = preg_replace(
            '/\s+/u',
            ' ',
            trim((string) $this->params()->fromPost('ten_loai', ''))
        );

        return [
            'ten_loai'   => $name ?? '',
            'mo_ta'      => trim((string) $this->params()->fromPost('mo_ta', '')),
            'trang_thai' => (int) $this->params()->fromPost('trang_thai', -1),
        ];
    }

    /**
     * @param array{ten_loai: string, mo_ta: string, trang_thai: int} $data
     * @return array<string, string>
     */
    private function validateForm(array $data, ?int $exceptId = null): array
    {
        $errors = [];

        if ($data['ten_loai'] === '') {
            $errors['ten_loai'] = 'Vui lòng nhập tên loại sách.';
        } elseif (mb_strlen($data['ten_loai']) > 150) {
            $errors['ten_loai'] = 'Tên loại sách không được dài quá 150 ký tự.';
        } elseif (preg_match('/[\p{L}\p{N}]/u', $data['ten_loai']) !== 1) {
            $errors['ten_loai'] = 'Tên loại sách phải có ít nhất một chữ cái hoặc chữ số.';
        } elseif ($this->loaiSachTable->nameExists($data['ten_loai'], $exceptId)) {
            $errors['ten_loai'] = 'Tên loại sách đã tồn tại.';
        }

        if (mb_strlen($data['mo_ta']) > 1000) {
            $errors['mo_ta'] = 'Mô tả không được dài quá 1000 ký tự.';
        }

        if (! in_array($data['trang_thai'], [0, 1], true)) {
            $errors['trang_thai'] = 'Trạng thái loại sách không hợp lệ.';
        }

        return $errors;
    }

    private function isAuthenticated(): bool
    {
        $session = new Container('user_session');

        return isset($session->user);
    }
}
