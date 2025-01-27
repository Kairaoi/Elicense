<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryContract
{
    public function all(array $columns = ['*']): Collection;
    public function count(): int;
    public function create(array $data): Model;
    public function update(int $id, array $data): Model;
    public function createMultiple(array $data): bool;
    public function deleteById($id): bool;
    public function deleteMultipleById(array $ids): int;
    public function first(array $columns = ['*']): ?Model;
    public function get(array $columns = ['*']): Collection;
    public function getById($id, array $columns = ['*']): ?Model;
    public function getByColumn($item, $column, array $columns = ['*']): ?Model;
    public function paginate($limit = 25, array $columns = ['*'], $pageName = 'page', $page = null): LengthAwarePaginator;
    public function updateById($id, array $data, array $options = []): bool;
    public function limit($limit);
    public function orderBy($column, $direction = 'asc');
    public function where($column, $value, $operator = '=');
    public function whereIn($column, $values);
    public function with($relations);
}
