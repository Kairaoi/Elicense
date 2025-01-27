<?php

namespace App\Repositories\Pfps;

use App\Models\Pfps\VisitorApplication; // Make sure this points to your correct VisitorApplication model
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

use App\Repositories\CustomBaseRepository;
use DB;

class VisitorApplicationRepository extends CustomBaseRepository
{
    /**
     * Return the model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return VisitorApplication::class; // Ensure this matches your VisitorApplication model
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
        string $order_by = 'application_id',
        string $sort = 'asc',
        bool $trashed = false
    ): Collection {
        // Initialize query with necessary relationships
        $query = $this->getModelInstance()
            ->with([
                'visitor:visitor_id,first_name,last_name,passport_number',
                'category:category_id,category_name',
                'activityType:activity_type_id,activity_name',
                'duration:duration_id,duration_name'
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
                    ->orWhereRaw('LOWER(COALESCE(rejection_reason, \'\')) LIKE ?', [$searchTerm])
                    // Join and search visitor information
                    ->orWhereHas('visitor', function ($q) use ($searchTerm) {
                        $q->where(function($subQ) use ($searchTerm) {
                            $subQ->whereRaw('LOWER(first_name) LIKE ?', [$searchTerm])
                                ->orWhereRaw('LOWER(last_name) LIKE ?', [$searchTerm])
                                ->orWhereRaw('LOWER(passport_number) LIKE ?', [$searchTerm]);
                        });
                    })
                    // Join and search category information
                    ->orWhereHas('category', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(category_name) LIKE ?', [$searchTerm]);
                    })
                    // Join and search activity type information
                    ->orWhereHas('activityType', function ($q) use ($searchTerm) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm]);
                    });
            });
        }
    
        // Validate and apply ordering
        $validOrderBy = [
            'application_id',
            'status',
            'application_date',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ];
    
        $sort = strtolower($sort) === 'desc' ? 'desc' : 'asc';
        
        if (in_array($order_by, $validOrderBy)) {
            $query->orderBy($order_by, $sort);
        } else {
            $query->orderBy('application_id', $sort);
        }
    
        // Add index hints for performance if needed
        $query->from('visitor_applications', 'visitor_applications');
        
        return $query->select([
            'application_id',
            'visitor_id',
            'category_id',
            'activity_type_id',
            'duration_id',
            'status',
            'rejection_reason',
            'application_date',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at'
        ])->get();
    }

    /**
     * Get a list of visitor application status, keyed by their IDs.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck(): \Illuminate\Support\Collection
    {
        return DB::table('visitor_applications')
            ->select(
                'visitor_applications.application_id',
                DB::raw("CONCAT('Visitor: ', visitors.full_name, ' (Status: ', visitor_applications.status, ')') AS display_text")
            )
            ->join('visitors', 'visitor_applications.visitor_id', '=', 'visitors.visitor_id')
            ->pluck('display_text', 'application_id');
    }
}
