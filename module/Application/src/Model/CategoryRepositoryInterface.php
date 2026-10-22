<?php

declare(strict_types=1);

namespace Application\Model;

interface CategoryRepositoryInterface
{
    /** @return list<array{id: int, category_code: string, category_name: string, status: int}> */
    public function fetchAll(): array;

    /** @return array{id: int, category_code: string, category_name: string, status: int}|null */
    public function find(int $id): ?array;

    public function codeExists(string $code, ?int $exceptId = null): bool;

    public function nameExists(string $name, ?int $exceptId = null): bool;

    /** @param array{category_code: string, category_name: string, status: int} $data */
    public function insert(array $data): void;

    /** @param array{category_code: string, category_name: string, status: int} $data */
    public function update(int $id, array $data): void;
}
