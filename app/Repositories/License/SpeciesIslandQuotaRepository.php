<?php

namespace App\Repositories\License;

use App\Models\License\SpeciesIslandQuota; // Ensure this is the correct model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use Carbon\Carbon;
use DB;

class SpeciesIslandQuotaRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return SpeciesIslandQuota::class; // Ensure this matches your model
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

        // Join the species and island tables
        $query->join('species', 'species_island_quotas.species_id', '=', 'species.id')
              ->join('islands', 'species_island_quotas.island_id', '=', 'islands.id')
              ->select('species_island_quotas.*', 
                       'species.name as species_name', 
                       'islands.name as island_name');

        // Search logic: check if $search is not empty
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';  // Make the search term case-insensitive and add wildcards
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(species.name) LIKE ?', [$search])
                      ->orWhereRaw('LOWER(islands.name) LIKE ?', [$search])
                      ->orWhere('year', 'LIKE', $search)
                      ->orWhere('island_quota', 'LIKE', $search)
                      ->orWhere('remaining_quota', 'LIKE', $search);
            });
        }

        // Ensure ordering by valid columns
        $validOrderBy = ['id', 'species_id', 'island_id', 'year', 'island_quota', 'remaining_quota', 'species_name', 'island_name'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);  // Apply sorting
        }

        // Fetch the result and map Carbon to the date fields
        return $query->get()->map(function ($quota) {
            // Use Carbon to format the dates
            $quota->created_at = Carbon::parse($quota->created_at)->diffForHumans();
            return $quota;
        });
    }

    /**
     * Get a list of species island quotas for a specific year.
     *
     * @param int $year
     * @return Collection
     */
    public function getByYear(int $year): Collection
    {
        return $this->getModelInstance()->where('year', $year)->get();
    }

    /**
     * Get a species island quota by its ID with optional relationships and columns.
     *
     * @param int|string $id
     * @param array $columns
     * @return Model|null
     */
    public function getById($id, array $columns = ['*']): ?Model
    {
        $query = $this->getModelInstance()->newQuery();

        // Check if any of the columns are actually relationship names
        $relationships = [];
        foreach ($columns as $key => $column) {
            if (method_exists($this->getModelInstance(), $column)) {
                $relationships[] = $column;
                unset($columns[$key]);
            }
        }

        // If there are relationships, load them
        if (!empty($relationships)) {
            $query->with($relationships);
        }

        // If specific columns are requested, select them
        if ($columns !== ['*'] && !empty($columns)) {
            $query->select($columns);
        }

        return $query->findOrFail($id);
    }

    /**
     * Add a record for species island quota item.
     *
     * @param int $speciesIslandQuotaId
     * @param array $data
     * @return \App\Models\SpeciesIslandQuota\SpeciesIslandQuotaItem
     */
    public function addQuotaItem(int $speciesIslandQuotaId, array $data): Model
    {
        // Set created_by to the authenticated user or null if not authenticated
        $data['created_by'] = auth()->id(); // This can be null for unauthenticated users

        // Create the quota item
        return SpeciesIslandQuotaItem::create(array_merge($data, ['species_island_quota_id' => $speciesIslandQuotaId]));
    }
}