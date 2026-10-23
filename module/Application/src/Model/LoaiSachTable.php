<?php

declare(strict_types=1);

namespace Application\Model;

use Laminas\Db\TableGateway\TableGatewayInterface;

class LoaiSachTable
{
    private TableGatewayInterface $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getAll(): array
    {
        return $this->tableGateway->select()->toArray();
    }

    public function getById(int $id): ?array
    {
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();

        return $row ? (array) $row : null;
    }

    public function save(array $data): void
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;

        if ($id === 0) {
            unset($data['id']);
            $this->tableGateway->insert($data);
            return;
        }

        if ($this->getById($id) === null) {
            throw new \RuntimeException(
                'Không tìm thấy loại sách có ID ' . $id
            );
        }

        unset($data['id']);
        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}
