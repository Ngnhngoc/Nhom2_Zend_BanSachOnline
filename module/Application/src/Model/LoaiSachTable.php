<?php

declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;
use RuntimeException;

class LoaiSachTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Lấy tất cả loại sách.
     */
    public function getAll(): array
    {
        return $this->tableGateway->select()->toArray();
    }

    /**
     * Tìm kiếm loại sách theo từ khóa (tên loại, mã loại, mô tả) và trạng thái.
     */
    public function search(?string $keyword = null, ?int $trangThai = null): array
    {
        $keyword = trim((string) $keyword);

        return $this->tableGateway->select(function ($select) use ($keyword, $trangThai) {
            $predicate = $select->where;

            if ($keyword !== '') {
                $cleanId = preg_replace('/^[Ll]0*/', '', $keyword);
                if (is_numeric($cleanId) && (int) $cleanId > 0) {
                    $predicate->nest()
                        ->equalTo('id', (int) $cleanId)
                        ->or
                        ->like('ten_loai', '%' . $keyword . '%')
                        ->or
                        ->like('mo_ta', '%' . $keyword . '%')
                    ->unnest();
                } else {
                    $predicate->nest()
                        ->like('ten_loai', '%' . $keyword . '%')
                        ->or
                        ->like('mo_ta', '%' . $keyword . '%')
                    ->unnest();
                }
            }

            if ($trangThai !== null && $trangThai !== -1) {
                $predicate->equalTo('trang_thai', $trangThai);
            }

            $select->order('id ASC');
        })->toArray();
    }

    /**
     * Lấy loại sách theo ID.
     */
    public function getById(int $id): ?array
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        return $row ? (array) $row : null;
    }

    /**
     * Thêm mới hoặc cập nhật loại sách.
     */
    public function save(array $data): void
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;

        // Chỉ cho phép các trường thuộc bảng LoaiSach.
        $allowedFields = [
            'ten_loai',
            'mo_ta',
            'trang_thai',
        ];

        $data = array_intersect_key(
            $data,
            array_flip($allowedFields)
        );

        if ($id === 0) {
            if (empty($data['ten_loai'])) {
                throw new RuntimeException(
                    'Tên loại sách không được để trống.'
                );
            }

            // ngay_tao và ngay_cap_nhat do MySQL tự tạo.
            $this->tableGateway->insert($data);
            return;
        }

        if ($this->getById($id) === null) {
            throw new RuntimeException(
                'Không tìm thấy loại sách có ID ' . $id
            );
        }

        if (empty($data['ten_loai'])) {
            throw new RuntimeException(
                'Tên loại sách không được để trống.'
            );
        }

        // MySQL tự cập nhật ngay_cap_nhat.
        $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Xóa loại sách theo ID.
     */
    public function delete(int $id): void
    {
        if ($this->getById($id) === null) {
            throw new RuntimeException(
                'Không tìm thấy loại sách có ID ' . $id
            );
        }

        try {
            $this->tableGateway->delete(['id' => $id]);
        } catch (\Throwable $e) {
            // Không thể xóa nếu còn đầu sách tham chiếu loại sách.
            throw new RuntimeException(
                'Không thể xóa loại sách này. '
                . 'Hãy kiểm tra xem còn đầu sách thuộc loại này không.',
                0,
                $e
            );
        }
    }
}
