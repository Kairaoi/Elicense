<?php

namespace App\Repositories\Pfps;

use App\Models\Pfps\ActivityType; // Make sure this points to your correct ActivityType model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use App\Repositories\CustomBaseRepository;
use DB;

class ActivityTypeRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return ActivityType::class; // Ensure this matches your ActivityType model
    }

    /**
     * Count the number of specified model records in the database.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->getModelInstance()->count();
    }

    /**
     * Create a new model record in the database.
     *
     * @param array $data
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create($data);
    }

    /**
     * Update an existing model record in the database.
     *
     * @param int $id
     * @param array $data
     *
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->getModelInstance()->findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * Get data for DataTables with optional search and sorting.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     *
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = 'activity_type_id', $sort = 'asc', $trashed = false): Collection
{
    // Initialize the query builder for the activity_types table
    $query = $this->getModelInstance()->newQuery();
    
    // Include soft-deleted records if $trashed is true
    if ($trashed) {
        $query->withTrashed(); // Include soft-deleted records
    }

    // Eager load the category relationship to prevent null errors
    $query->with('category'); // Ensure that the category is loaded

    // Apply search logic if $search is not empty
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%'; // Make the search term case-insensitive and add wildcards
        $query->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(activity_name) LIKE ?', [$search]);
        });
    }

    // Ensure ordering by valid columns
    $validOrderBy = ['activity_type_id', 'activity_name', 'category_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];
    if (in_array($order_by, $validOrderBy)) {
        $query->orderBy($order_by, $sort);
    }

    // Return the query results
    return $query->distinct()->get(); // Or use paginate() for pagination
}

    

    /**
     * Get a list of activity names keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('activity_types')
            ->select(
                'activity_types.activity_type_id',
                DB::raw("CONCAT(activity_types.activity_name, ' (Category: ', permit_categories.category_name, ')') AS display_text")
            )
            ->join('permit_categories', 'permit_categories.category_id', '=', 'activity_types.category_id')
            ->pluck('display_text', 'activity_type_id');
    }
}
