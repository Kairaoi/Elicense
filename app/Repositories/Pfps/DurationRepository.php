<?php

namespace App\Repositories\Pfps;

use App\Models\Pfps\Duration; // Make sure this points to your correct Duration model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use App\Repositories\CustomBaseRepository;
use DB;

class DurationRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Duration::class; // Ensure this matches your Duration model
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
    public function getForDataTable($search = '', $order_by = 'duration_id', $sort = 'asc', $trashed = false): Collection
    {
        // Initialize the query builder for the durations table
        $query = $this->getModelInstance()->newQuery();
    
        // Include soft-deleted records if $trashed is true
        if ($trashed) {
            $query->withTrashed(); // Include soft-deleted records
        }
    
        // Apply search logic if $search is not empty
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%'; // Make the search term case-insensitive and add wildcards
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(duration_name) LIKE ?', [$search]);
            });
        }
    
        // Ensure ordering by valid columns
        $validOrderBy = ['duration_id', 'duration_name', 'fee_amount', 'created_by', 'updated_by', 'created_at', 'updated_at'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);
        }
    
        // Return the query results
        return $query->distinct()->get(); // Or use paginate() for pagination
    }

    /**
     * Get a list of duration names keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('durations')
            ->select(
                'durations.duration_id',
                DB::raw("
                    CONCAT(
                        durations.duration_name, 
                        ' (Fee: ', 
                        CASE 
                            WHEN durations.is_extension THEN durations.extension_fee 
                            ELSE durations.initial_fee 
                        END, 
                        ')'
                    ) AS display_text
                ")
            )
            ->pluck('display_text', 'duration_id');
    }
    
}
