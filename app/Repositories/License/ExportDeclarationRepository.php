<?php

namespace App\Repositories\License;

use App\Models\License\ExportDeclaration; // Ensure this is the correct model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use DB;

class ExportDeclarationRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return ExportDeclaration::class; // Ensure this matches your model
    }

    /**
     * Count the number of export declaration records in the database.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->getModelInstance()->count();
    }

    /**
     * Create a new export declaration record in the database.
     *
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create($data);
    }

    /**
     * Update an existing export declaration record in the database.
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
    $query = $this->getModelInstance()->newQuery(); // Initialize the query builder

    // Include soft-deleted records if $trashed is true
    if ($trashed) {
        $query->withTrashed();
    } else {
        $query->withoutTrashed();
    }

    // Use a subquery to limit licenses to one per applicant
    $query->join('applicants', 'export_declarations.applicant_id', '=', 'applicants.id')
          ->join('licenses', function ($join) {
              $join->on('export_declarations.applicant_id', '=', 'licenses.applicant_id')
                   ->where('licenses.id', '=', function ($query) {
                       $query->select('id')
                             ->from('licenses')
                             ->whereColumn('licenses.applicant_id', 'export_declarations.applicant_id')
                             ->orderBy('created_at', 'desc') // Example: select the most recent license
                             ->limit(1);
                   });
          })
          ->join('license_types', 'licenses.license_type_id', '=', 'license_types.id') // Join with license_types
          ->select(
              'export_declarations.*', 
              'applicants.first_name', 
              'applicants.last_name', 
              'applicants.company_name',
              'license_types.name as license_type' // Include license type
          );

    // Search logic
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';
        $query->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(export_destination) LIKE ?', [$search])
                  ->orWhereRaw('CAST(shipment_date AS TEXT) LIKE ?', [$search])
                  ->orWhereRaw('CAST(total_license_fee AS TEXT) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(applicants.first_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(applicants.last_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(applicants.company_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(license_types.name) LIKE ?', [$search]); // Search by license type
        });
    }

    // Ensure ordering by valid columns
    $validOrderBy = ['id', 'shipment_date', 'export_destination', 'total_license_fee', 'applicants.first_name', 'applicants.last_name', 'applicants.company_name', 'license_types.name'];
    if (in_array($order_by, $validOrderBy)) {
        $query->orderBy($order_by, $sort);
    }

    // Return query results
    return $query->distinct()->get();
}


    /**
     * Get a list of export declarations for applicants.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('export_declarations')
            ->select(
                'export_declarations.id',
                DB::raw("CONCAT('Shipment: ', export_declarations.shipment_date, ' to ', export_declarations.export_destination, ' (Fee: ', export_declarations.total_license_fee, ')') AS display_text")
            )
            ->pluck('display_text', 'id');
    }

    public function getSpeciesForDeclaration($declarationId)
{
    return DB::table('declaration_species')
        ->join('species', 'declaration_species.species_id', '=', 'species.id')
        ->where('export_declaration_id', $declarationId)
        ->select([
            'species.id',
            'species.common_name',
            'species.scientific_name',
            'declaration_species.volume_kg',
            'declaration_species.unit_price'
        ])
        ->orderBy('species.common_name')
        ->get();
}

}
