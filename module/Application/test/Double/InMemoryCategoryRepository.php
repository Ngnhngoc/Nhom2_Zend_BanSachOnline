<?php

declare(strict_types=1);

namespace ApplicationTest\Double;

use Application\Model\CategoryRepositoryInterface;

final class InMemoryCategoryRepository implements CategoryRepositoryInterface
{
    /** @var list<array{id: int, category_code: string, category_name: string, status: int}> */
    private array $categories;

    /**
     * @param list<array{id: int, category_code: string, category_name: string, status: int}> $categories
     */
    public function __construct(array $categories = [])
    {
        $this->categories = $categories;
    }

    public function fetchAll(): array
    {
        return $this->categories;
    }

    public function find(int $id): ?array
    {
        foreach ($this->categories as $category) {
            if ($category['id'] === $id) {
                return $category;
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
        $ids    = array_column($this->categories, 'id');
        $nextId = ($ids === [] ? 0 : max($ids)) + 1;

        $this->categories[] = ['id' => $nextId] + $data;
    }

    public function update(int $id, array $data): void
    {
        foreach ($this->categories as $index => $category) {
            if ($category['id'] === $id) {
                $this->categories[$index] = ['id' => $id] + $data;
                return;
            }
        }
    }

    /** @param 'category_code'|'category_name' $field */
    private function valueExists(string $field, string $value, ?int $exceptId): bool
    {
        foreach ($this->categories as $category) {
            if ($category['id'] !== $exceptId && strcasecmp($category[$field], $value) === 0) {
                return true;
            }
        }

        return false;
    }
}
