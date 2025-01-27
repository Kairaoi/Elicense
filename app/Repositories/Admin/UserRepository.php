<?php

namespace App\Repositories\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\CustomBaseRepository;
use Spatie\Permission\Models\Role;
use DB;

class UserRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Get DataTable of users with optional search.
     *
     * @param string|null $search
     * @return Collection
     */
    public function getForDataTable($search = null): Collection
    {
        $query = $this->getModelInstance()->newQuery();

        // Search logic
        if ($search) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
            });
        }

        // Include roles for displaying in DataTable
        $query->with('roles')->select(['id', 'name', 'email', 'created_at']);

        return $query->get();
    }

    /**
     * Create a new user in the database.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        // Encrypt the password before creating the user
        $data['password'] = bcrypt($data['password']);
        return $this->getModelInstance()->create($data);
    }

    /**
     * Update a user in the database.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $user = $this->getModelInstance()->findOrFail($id);

        // Update password only if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * Delete a user by ID.
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
     * Retrieve all roles for assigning to users.
     *
     * @return Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }
}
