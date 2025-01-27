<?php

namespace App\Repositories\License;

use App\Models\License\Applicant; // Ensure this is the correct model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\CustomBaseRepository;
use DB;

class ApplicantsRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Applicant::class; // Ensure this matches your model
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
                $query->whereRaw('LOWER(first_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(company_name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(citizenship) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(contact_address) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(phone_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$search]);
            });
        }

        // Ensure ordering by valid columns
        $validOrderBy = ['id', 'first_name', 'last_name', 'company_name', 'citizenship', 'contact_address', 'phone_number', 'email'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);  // Apply sorting
        }

        // Return the query results, optionally paginated
        return $query->distinct()->get();  // Or use paginate() for pagination
    }

    /**
     * Get a list of sea cucumber license applicants with their species names keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
{
    return DB::table('applicants')
        ->select(
            'applicants.id',
            DB::raw("CONCAT(applicants.first_name, ' ', applicants.last_name, ' (Company: ', applicants.company_name, ', Citizenship: ', applicants.citizenship, ', ID: ', applicants.id, ')') AS display_text")
        )
        ->pluck('display_text', 'id');
}


   
}
