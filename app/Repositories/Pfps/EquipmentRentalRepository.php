<?php

namespace App\Repositories\Pfps;

use App\Models\Pfps\EquipmentRental; // Ensure this points to your correct EquipmentRental model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use DB;

class EquipmentRentalRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return EquipmentRental::class; // Ensure this matches your EquipmentRental model
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
    public function getForDataTable($search = '', $order_by = 'rental_id', $sort = 'asc', $trashed = false): Collection
    {
        // Initialize the query builder for the equipment_rentals table
        $query = $this->getModelInstance()->newQuery();
        
        // Include soft-deleted records if $trashed is true
        if ($trashed) {
            $query->withTrashed(); // Include soft-deleted records
        }

        // Eager load the permit relationship to prevent null errors
        $query->with('permit'); // Ensure that the permit is loaded

        // Apply search logic if $search is not empty
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%'; // Make the search term case-insensitive and add wildcards
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(equipment_type) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(currency) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(rental_fee) LIKE ?', [$search]);
            });
        }

        // Ensure ordering by valid columns
        $validOrderBy = ['rental_id', 'permit_id', 'equipment_type', 'rental_fee', 'currency', 'rental_date', 'return_date', 'created_by', 'updated_by', 'created_at', 'updated_at'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);
        }

        // Return the query results
        return $query->distinct()->get(); // Or use paginate() for pagination
    }

    /**
     * Get a list of equipment types keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('equipment_rentals')
            ->select(
                'equipment_rentals.rental_id',
                DB::raw("CONCAT(equipment_rentals.equipment_type, ' (Fee: ', equipment_rentals.rental_fee, ')') AS display_text")
            )
            ->pluck('display_text', 'rental_id');
    }
}
