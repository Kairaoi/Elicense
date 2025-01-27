<?php

namespace App\Repositories\Pfps;

use App\Models\Pfps\Permit; // Ensure this points to your correct Permit model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use App\Repositories\CustomBaseRepository;
use DB;

class PermitRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return Permit::class; // Ensure this matches your Permit model
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
    public function getForDataTable($search = '', $order_by = 'permit_id', $sort = 'asc', $trashed = false): Collection
    {
        // Initialize the query builder for the permits table
        $query = $this->getModelInstance()->newQuery();
    
        // Include soft-deleted records if $trashed is true
        if ($trashed) {
            $query->withTrashed(); // Include soft-deleted records
        }
    
        // Apply search logic if $search is not empty
        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%'; // Make the search term case-insensitive and add wildcards
            $query->where(function ($query) use ($search) {
                $query->whereRaw('LOWER(permit_number) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(permit_type) LIKE ?', [$search]);
            });
        }
    
        // Ensure ordering by valid columns
        $validOrderBy = ['permit_id', 'permit_number', 'issue_date', 'expiry_date', 'permit_type', 'created_by', 'updated_by', 'created_at', 'updated_at'];
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);
        }
    
        // Return the query results
        return $query->distinct()->get(); // Or use paginate() for pagination
    }

    /**
     * Get a list of permits with their associated visitor application and invoice info.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('permits')
            ->select(
                'permits.permit_id',
                DB::raw("CONCAT('Permit Number: ', permits.permit_number, ' (Issued on: ', permits.issue_date, ')') AS display_text")
            )
            ->join('visitor_applications', 'permits.application_id', '=', 'visitor_applications.application_id')
            ->join('invoices', 'permits.invoice_id', '=', 'invoices.invoice_id')
            ->pluck('display_text', 'permit_id');
    }
}
