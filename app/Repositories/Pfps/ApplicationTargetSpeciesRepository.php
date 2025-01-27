<?php

namespace App\Repositories\Pfps;

use App\Models\Pfps\ApplicationTargetSpecies; // Ensure this points to your correct ApplicationTargetSpecies model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use DB;

class ApplicationTargetSpeciesRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return ApplicationTargetSpecies::class; // Ensure this matches your ApplicationTargetSpecies model
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
    public function getForDataTable(
        string $search = '',
        string $order_by = 'application_target_species_id',
        string $sort = 'asc',
        bool $trashed = false
    ): Collection {
        // Initialize query with necessary relationships
        $query = $this->getModelInstance()
            ->with([
                'application:application_id,status',
                'species:species_id,common_name'
            ]);

        // Handle soft deletes
        if ($trashed) {
            $query->withTrashed();
        }

        // Apply search filters if search term exists
        if (!empty($search)) {
            $searchTerm = '%' . strtolower(trim($search)) . '%';

            $query->where(function ($query) use ($searchTerm) {
                $query->whereRaw('LOWER(status) LIKE ?', [$searchTerm])
                    // Join and search visitor application information
                    ->orWhereHas('application', function ($q) use ($searchTerm) {
                        $q->where(function($subQ) use ($searchTerm) {
                            $subQ->whereRaw('LOWER(status) LIKE ?', [$searchTerm]);
                        });
                    })
                    // Join and search species information
                    ->orWhereHas('species', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(common_name) LIKE ?', [$searchTerm]);
                    });
            });
        }

        // Validate and apply ordering
        $validOrderBy = [
            'application_target_species_id',
            'application_id',
            'species_id',
            'created_at',
            'updated_at'
        ];

        $sort = strtolower($sort) === 'desc' ? 'desc' : 'asc';

        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);
        } else {
            $query->orderBy('application_target_species_id', $sort);
        }

        // Add index hints for performance if needed
        $query->from('application_target_species', 'application_target_species');

        return $query->select([
            'application_target_species_id',
            'application_id',
            'species_id',
            'created_at',
            'updated_at'
        ])->get();
    }

    /**
     * Get a list of application target species status, keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('application_target_species')
            ->select(
                'application_target_species.application_target_species_id',
                DB::raw("CONCAT('Application: ', visitor_applications.status, ' - Species: ', target_species.common_name) AS display_text")
            )
            ->join('visitor_applications', 'application_target_species.application_id', '=', 'visitor_applications.application_id')
            ->join('target_species', 'application_target_species.species_id', '=', 'target_species.species_id')
            ->pluck('display_text', 'application_target_species_id');
    }
}
