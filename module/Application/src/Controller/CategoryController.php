<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\CategoryRepositoryInterface;
use Laminas\Http\Request;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger;
use Laminas\Stdlib\ParametersInterface;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use Throwable;

/** @psalm-suppress PropertyNotSetInConstructor */
class CategoryController extends AbstractActionController
{
    public function __construct(private readonly CategoryRepositoryInterface $categoryRepository)
    {
    }

    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'categories' => $this->categoryRepository->fetchAll(),
        ]);
    }

    /** @psalm-suppress PossiblyUnusedMethod Called by the Laminas router. */
    public function addAction(): ViewModel|Response
    {
        $formData = $this->emptyFormData();
        $errors   = [];
        if ($this->isPostRequest()) {
            $formData = $this->readFormData();
            $errors   = $this->validate($formData);
            if ($errors === []) {
                try {
                    $this->categoryRepository->insert($formData);
                    $this->flashMessengerPlugin()->addSuccessMessage('Thêm loại sách thành công.');
                    return $this->redirect()->toRoute('category');
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

    /** @psalm-suppress PossiblyUnusedMethod Called by the Laminas router. */
    public function editAction(): ViewModel|Response
    {
        $routeMatch = $this->getEvent()->getRouteMatch();
        $id         = (int) ($routeMatch?->getParam('id', 0) ?? 0);
        if ($id < 1) {
            $this->flashMessengerPlugin()->addErrorMessage('Mã loại sách không hợp lệ.');
            return $this->redirect()->toRoute('category');
        }

        $category = $this->categoryRepository->find($id);
        if ($category === null) {
            $this->flashMessengerPlugin()->addErrorMessage('Không tìm thấy loại sách cần sửa.');
            return $this->redirect()->toRoute('category');
        }

        $formData = [
            'category_code' => $category['category_code'],
            'category_name' => $category['category_name'],
            'status'        => $category['status'],
        ];
        $errors = [];
        if ($this->isPostRequest()) {
            $formData = $this->readFormData();
            $errors   = $this->validate($formData, $id);
            if ($errors === []) {
                try {
                    $this->categoryRepository->update($id, $formData);
                    $this->flashMessengerPlugin()->addSuccessMessage('Cập nhật loại sách thành công.');
                    return $this->redirect()->toRoute('category');
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

    /** @return array{category_code: string, category_name: string, status: int} */
    private function emptyFormData(): array
    {
        return [
            'category_code' => '',
            'category_name' => '',
            'status'        => 1,
        ];
    }

    /**
     * @return array{category_code: string, category_name: string, status: int}
     * @psalm-suppress MixedAssignment Laminas Request::getPost() is declared as mixed.
     */
    private function readFormData(): array
    {
        $request        = $this->getRequest();
        $postParameters = $request instanceof Request ? $request->getPost() : null;
        $post           = $postParameters instanceof ParametersInterface ? $postParameters->toArray() : [];
        $name = preg_replace('/\s+/u', ' ', trim((string) ($post['category_name'] ?? '')));
        return [
            'category_code' => strtoupper(trim((string) ($post['category_code'] ?? ''))),
            'category_name' => $name === null ? '' : $name,
            'status'        => (int) ($post['status'] ?? -1),
        ];
    }

    /**
     * @param array{category_code: string, category_name: string, status: int} $data
     * @return array<string, string>
     */
    private function validate(array $data, ?int $exceptId = null): array
    {
        $errors = [];
        if ($data['category_code'] === '') {
            $errors['category_code'] = 'Vui lòng nhập mã loại sách.';
        } elseif (strlen($data['category_code']) > 20) {
            $errors['category_code'] = 'Mã loại sách không được dài quá 20 ký tự.';
        } elseif (preg_match('/^[A-Z0-9_-]+$/', $data['category_code']) !== 1) {
            $errors['category_code'] = 'Mã chỉ được gồm chữ cái, số, dấu gạch ngang hoặc gạch dưới.';
        } elseif ($this->categoryRepository->codeExists($data['category_code'], $exceptId)) {
            $errors['category_code'] = 'Mã loại sách đã tồn tại.';
        }

        if ($data['category_name'] === '') {
            $errors['category_name'] = 'Vui lòng nhập tên loại sách.';
        } elseif (mb_strlen($data['category_name']) > 150) {
            $errors['category_name'] = 'Tên loại sách không được dài quá 150 ký tự.';
        } elseif ($this->categoryRepository->nameExists($data['category_name'], $exceptId)) {
            $errors['category_name'] = 'Tên loại sách đã tồn tại.';
        }

        if (! in_array($data['status'], [0, 1], true)) {
            $errors['status'] = 'Trạng thái không hợp lệ.';
        }

        return $errors;
    }

    private function isPostRequest(): bool
    {
        $request = $this->getRequest();

        return $request instanceof Request && $request->isPost();
    }

    private function flashMessengerPlugin(): FlashMessenger
    {
        $plugin = $this->plugin('flashMessenger');
        if (! $plugin instanceof FlashMessenger) {
            throw new RuntimeException('Flash Messenger plugin is not configured.');
        }

        return $plugin;
    }
}
