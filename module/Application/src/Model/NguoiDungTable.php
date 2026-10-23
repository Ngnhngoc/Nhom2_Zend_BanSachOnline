<?php

declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;

class NguoiDungTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(
        TableGatewayInterface $tableGateway
    ) {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Lấy danh sách người dùng.
     */
    public function getAll(): array
    {
        return $this->tableGateway->select()->toArray();
    }

    /**
     * Lấy một người dùng theo ID.
     */
    public function getById(int $id): ?array
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        return $row ? (array) $row : null;
    }

    /**
     * Tìm người dùng theo tên đăng nhập.
     */
    public function getByUsername(string $username): ?array
    {
        $rowset = $this->tableGateway->select([
            'username' => $username,
        ]);

        $row = $rowset->current();

        return $row ? (array) $row : null;
    }
}
