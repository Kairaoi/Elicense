<?php

namespace App\Repositories\Admin;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class PermissionRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Permission::class;
    }

    /**
     * Get a DataTable-ready collection of permissions with optional search.
     *
     * @param string|null $search
     * @return Collection
     */
    public function getForDataTable($search = null): Collection
    {
        $query = $this->getModelInstance()->newQuery();

        if ($search) {
            $search = '%' . strtolower($search) . '%';
            $query->whereRaw('LOWER(name) LIKE ?', [$search]);
        }

        $query->select(['id', 'name', 'created_at']);

        return $query->get();
    }

    /**
     * Create a new permission in the database.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create(['name' => $data['name']]);
    }

    /**
     * Update an existing permission.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $permission = $this->getModelInstance()->findOrFail($id);
        $permission->update(['name' => $data['name']]);
        return $permission;
    }

    /**
     * Delete a permission by ID.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteById($id): bool
    {
        $model = $this->getModelInstance()->find($id);
        return $model ? $model->delete() : false;
    }

    /**
     * Retrieve all permissions for display or selection purposes.
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return $this->getModelInstance()->all();
    }
}
