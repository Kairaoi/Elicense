<?php

namespace App\Repositories\License;

use App\Models\License\Species; // Make sure this points to your correct model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use App\Repositories\CustomBaseRepository;
use DB;

class SpeciesRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Species::class; // Ensure this matches your model
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
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false): Collection
    {
        $query = $this->getModelInstance()->newQuery();  // Initialize the query builder

        // Include soft-deleted records if $trashed is true
        if ($trashed) {
            $query->withTrashed();  // Include soft-deleted records
        } else {
            $query->withoutTrashed();  // Exclude soft-deleted records
        }

        // Search logic: check if $search is not empty
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';  // Make the search term case-insensitive and add wildcards
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [$search]);
            });
        }

        // Ensure ordering by valid columns
        $validOrderBy = ['id', 'name', 'license_type_id', 'quota', 'unit_price', 'created_at', 'updated_at'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);  // Apply sorting
        }

        // Return the query results
        return $query->distinct()->get();  // Or use paginate() for pagination
    }

    /**
     * Get a list of species names keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('species')
            ->select(
                'species.id',
                DB::raw("CONCAT(species.name, ' (Quota: ', species.quota, ', Unit Price: ', species.unit_price, ')') AS display_text")
            )
            ->pluck('display_text', 'id');
    }

   

    public function getByLicenseType(int $licenseTypeId): Collection
{
    $species = $this->getModelInstance()
        ->where('license_type_id', $licenseTypeId)
        ->get();  // Returns Eloquent\Collection

    // Transform the data and still return an Eloquent\Collection
    foreach ($species as $item) {
        $item->display = "{$item->name} (Quota: {$item->quota}, Unit Price: {$item->unit_price})";
    }

    return $species;  // Still an Eloquent\Collection
}

public function getSpeciesForApplicant($applicantId)
{
    return DB::table('species')
        ->join('license_items', 'species.id', '=', 'license_items.species_id')
        ->join('licenses', 'license_items.license_id', '=', 'licenses.id')
        ->where('licenses.applicant_id', $applicantId)
        ->select(
            'species.id',
            DB::raw("CONCAT(species.name, ' (Quota: ', species.quota, ', Unit Price: ', species.unit_price, ')') AS display_text")
        )
        ->pluck('display_text', 'id');
}

}
