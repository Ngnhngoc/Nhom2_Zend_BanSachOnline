<?php

declare(strict_types=1);

namespace Application\Model;

use ArrayObject;
use Laminas\Db\Sql\Select;
use Laminas\Db\TableGateway\TableGateway;
use RuntimeException;

class CategoryTable implements CategoryRepositoryInterface
{
    public function __construct(private readonly TableGateway $tableGateway)
    {
    }

    /** @psalm-suppress MixedAssignment Laminas ResultSet does not expose a generic row type. */
    public function fetchAll(): array
    {
        $select = new Select($this->tableGateway->getTable());
        $select->order('id ASC');

        $categories = [];
        foreach ($this->tableGateway->selectWith($select) as $row) {
            if (! is_array($row) && ! is_object($row)) {
                continue;
            }

            $categories[] = $this->normalizeRow($row);
        }

        return $categories;
    }

    /** @psalm-suppress MixedAssignment Laminas ResultSet does not expose a generic row type. */
    public function find(int $id): ?array
    {
        foreach ($this->tableGateway->select(['id' => $id]) as $row) {
            if (is_array($row) || is_object($row)) {
                return $this->normalizeRow($row);
            }
        }

        return null;
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        return $this->valueExists('category_code', $code, $exceptId);
    }

    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        return $this->valueExists('category_name', $name, $exceptId);
    }

    public function insert(array $data): void
    {
        $this->tableGateway->insert($data);
    }

    public function update(int $id, array $data): void
    {
        if ($this->find($id) === null) {
            throw new RuntimeException(sprintf('Không tìm thấy loại sách có ID %d.', $id));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    private function valueExists(string $column, string $value, ?int $exceptId): bool
    {
        $select = new Select($this->tableGateway->getTable());
        $select->columns(['id'])->where([$column => $value])->limit(1);

        if ($exceptId !== null) {
            $select->where->notEqualTo('id', $exceptId);
        }

        foreach ($this->tableGateway->selectWith($select) as $_row) {
            return true;
        }

        return false;
    }

    /**
     * @param array<array-key, mixed>|object $row
     * @return array{id: int, category_code: string, category_name: string, status: int}
     */
    private function normalizeRow(array|object $row): array
    {
        if ($row instanceof ArrayObject) {
            $data = $row->getArrayCopy();
        } else {
            $data = is_array($row) ? $row : get_object_vars($row);
        }

        return [
            'id'            => (int) ($data['id'] ?? 0),
            'category_code' => (string) ($data['category_code'] ?? ''),
            'category_name' => (string) ($data['category_name'] ?? ''),
            'status'        => (int) ($data['status'] ?? 0),
        ];
    }
}
