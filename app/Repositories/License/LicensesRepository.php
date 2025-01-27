<?php

namespace App\Repositories\License;

use App\Models\License\License; // Ensure this is the correct model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use App\Models\License\LicenseItem;
use Carbon\Carbon;
use DB;

class LicensesRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return License::class; // Ensure this matches your model
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

    // Join the applicants and license_types tables
    $query->join('applicants', 'licenses.applicant_id', '=', 'applicants.id')
          ->join('license_types', 'licenses.license_type_id', '=', 'license_types.id')
          ->select('licenses.*', 
                   DB::raw("CONCAT(applicants.first_name, ' ', applicants.last_name) as full_name"), 
                   'license_types.name as license_type_name');

    // Search logic: check if $search is not empty
    if (!empty($search)) {
        $search = '%' . strtolower($search) . '%';  // Make the search term case-insensitive and add wildcards
        $query->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(applicants.first_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(applicants.last_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(applicants.company_name) LIKE ?', [$search])
                  ->orWhereRaw('LOWER(license_types.name) LIKE ?', [$search])
                  ->orWhereRaw('issue_date LIKE ?', [$search])
                  ->orWhereRaw('expiry_date LIKE ?', [$search])
                  ->orWhere('total_fee', 'LIKE', $search);
        });
    }

    // Ensure ordering by valid columns
    $validOrderBy = ['id', 'applicant_id', 'license_type_id', 'issue_date', 'expiry_date', 'total_fee', 'full_name', 'license_type_name'];
    if (in_array($order_by, $validOrderBy)) {
        if ($order_by === 'full_name') {
            // Order by full_name (concatenated first_name and last_name)
            $query->orderByRaw("CONCAT(applicants.first_name, ' ', applicants.last_name) $sort");
        } else {
            $query->orderBy($order_by, $sort);  // Apply sorting
        }
    }

    // Fetch the result and map Carbon to the date fields
    return $query->get()->map(function ($license) {
        // Use Carbon to format the dates
        $license->created_at = Carbon::parse($license->created_at)->diffForHumans();
        $license->issue_date = Carbon::parse($license->issue_date)->toFormattedDateString(); // Custom date format
        $license->expiry_date = Carbon::parse($license->expiry_date)->toFormattedDateString(); // Custom date format
        return $license;
    });
}


    /**
     * Get a list of licenses keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('licenses')
            ->select('licenses.id', 'licenses.issue_date', 'licenses.expiry_date', 'licenses.total_fee')
            ->pluck('total_fee', 'id');
    }

    /**
     * Get a list of licenses for a specific applicant.
     *
     * @param int $applicantId
     * @return Collection
     */
    public function getByApplicantId(int $applicantId): Collection
    {
        return $this->getModelInstance()->where('applicant_id', $applicantId)->get();
    }
/**
 * Get a license by its ID with optional relationships and columns.
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
    public function addLicenseItem(int $licenseId, array $data): LicenseItem
    {
        // Set created_by to the authenticated user or null if not authenticated
        $data['created_by'] = auth()->id(); // This can be null for unauthenticated users
    
        // Create the license item
        return LicenseItem::create(array_merge($data, ['license_id' => $licenseId]));
    }
    
    
}
