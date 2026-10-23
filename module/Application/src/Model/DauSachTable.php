<?php

declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;
use RuntimeException;

class DauSachTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(
        TableGatewayInterface $tableGateway
    ) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Lấy danh sách tất cả đầu sách.
     */
    public function getAll(): array
    {
        return $this->tableGateway->select()->toArray();
    }

    /**
     * Lấy đầu sách theo ID.
     */
    public function getById(int $id): ?array
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        return $row ? (array) $row : null;
    }

    /**
     * Lấy danh sách đầu sách theo loại sách.
     */
    public function getByLoaiSachId(int $loaiSachId): array
    {
        return $this->tableGateway
            ->select(['loai_sach_id' => $loaiSachId])
            ->toArray();
    }

    /**
     * Thêm mới hoặc cập nhật đầu sách.
     */
    public function save(array $data): void
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;

        $allowedFields = [
            'ten_sach',
            'tac_gia',
            'nha_xuat_ban',
            'gia_ban',
            'so_luong',
            'loai_sach_id',
        ];

        $data = array_intersect_key(
            $data,
            array_flip($allowedFields)
        );

        if (empty($data['ten_sach'])) {
            throw new RuntimeException(
                'Tên sách không được để trống.'
            );
        }

        if (
            !isset($data['loai_sach_id'])
            || (int) $data['loai_sach_id'] <= 0
        ) {
            throw new RuntimeException(
                'Vui lòng chọn loại sách hợp lệ.'
            );
        }

        if (
            isset($data['gia_ban'])
            && (float) $data['gia_ban'] < 0
        ) {
            throw new RuntimeException(
                'Giá bán không được âm.'
            );
        }

        if (
            isset($data['so_luong'])
            && (int) $data['so_luong'] < 0
        ) {
            throw new RuntimeException(
                'Số lượng không được âm.'
            );
        }

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if ($this->getById($id) === null) {
            throw new RuntimeException(
                'Không tìm thấy đầu sách có ID ' . $id
            );
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    /**
     * Xóa đầu sách theo ID.
     */
    public function delete(int $id): void
    {
        if ($this->getById($id) === null) {
            throw new RuntimeException(
                'Không tìm thấy đầu sách có ID ' . $id
            );
        }

        $this->tableGateway->delete(['id' => $id]);
    }
}
