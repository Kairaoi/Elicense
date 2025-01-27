<?php

namespace App\Repositories\Admin;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;

class RoleRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Role::class;
    }

    /**
     * Get a DataTable-ready collection of roles with optional search.
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
     * Create a new role in the database.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create(['name' => $data['name']]);
    }

    /**
     * Update an existing role.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $role = $this->getModelInstance()->findOrFail($id);
        $role->update(['name' => $data['name']]);
        return $role;
    }

    /**
     * Delete a role by ID.
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
     * Get all permissions for assigning to roles.
     *
     * @return Collection
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    /**
     * Assign permissions to a role.
     *
     * @param int $roleId
     * @param array $permissionIds
     * @return Model
     */
    public function assignPermissions(int $roleId, array $permissionIds): Model
    {
        $role = $this->getModelInstance()->findOrFail($roleId);
        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);

        return $role;
    }

    /**
     * Remove all permissions from a role.
     *
     * @param int $roleId
     * @return Model
     */
    public function removePermissions(int $roleId): Model
    {
        $role = $this->getModelInstance()->findOrFail($roleId);
        $role->revokePermissionTo($role->permissions);

        return $role;
    }
}
