<?php

namespace App\Repositories\License;

use App\Models\License\SpeciesTracking;
use App\Repositories\CustomBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DB;

class SpeciesTrackingRepository extends CustomBaseRepository
{
    public function model(): string
    {
        return SpeciesTracking::class;
    }

    /**
     * Create a new SpeciesTracking record.
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->getModelInstance()->create($data);
    }

    /**
     * Update an existing SpeciesTracking record.
     *
     * @param int $id
     * @param array $data
     * @return Model
     */
    public function update(int $id, array $data): Model
    {
        $model = $this->getModelInstance()->findOrFail($id);
        $model->update($data);
        return $model;
    }

    /**
     * Fetch data for DataTables.
     *
     * @param string $search
     * @param string $order_by
     * @param string $sort
     * @param bool $trashed
     * @return Collection
     */
    public function getForDataTable($search = '', $order_by = 'id', $sort = 'asc', $trashed = false): Collection
    {
        $query = $this->getModelInstance()->newQuery();

        if ($trashed) {
            $query->withTrashed();
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $searchLower = strtolower($search);
                $q->whereRaw('LOWER(year) LIKE ?', ['%' . $searchLower . '%'])
                  ->orWhereRaw('LOWER(quota_allocated) LIKE ?', ['%' . $searchLower . '%'])
                  ->orWhereRaw('LOWER(quota_used) LIKE ?', ['%' . $searchLower . '%'])
                  ->orWhereRaw('LOWER(remaining_quota) LIKE ?', ['%' . $searchLower . '%']);
            });
        }

        return $query->orderBy($order_by, $sort)
                     ->with(['species:id,name', 'agent:id,first_name,last_name', 'island:id,name', 'creator:id,name', 'updater:id,name']) // Replace with actual relationships
                     ->distinct()
                     ->get();
    }

    /**
     * Fetch a list of species tracking records for dropdowns or other simple lists.
     *
     * @return \Illuminate\Support\Collection
     */
    public function pluck($agentId = null, $islandId = null): \Illuminate\Support\Collection
    {
        try {
            // Build the query
            $query = DB::table('species_tracking as st')
                ->join('species as s', 's.id', '=', 'st.species_id')
                ->select(
                    'st.id',
                    DB::raw("CONCAT(s.name, ' - Quota: ', st.quota_allocated, 
                            'kg, Used: ', COALESCE(st.quota_used, 0), 'kg, Remaining: ', 
                            st.remaining_quota, 'kg, Year: ', st.year) AS display_text")
                )
                ->whereNull('st.deleted_at')
                ->orderBy('st.year', 'desc')
                ->orderBy('s.name');
    
            // Log the SQL query without the 'remaining_quota' filter
            \Log::info('Species query without quota filter:', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
    
            // Optionally, remove the 'remaining_quota' filter for debugging
            return $query->pluck('display_text', 'st.id');
        } catch (\Exception $e) {
            \Log::error('Error in SpeciesTrackingRepository pluck: ' . $e->getMessage());
            throw $e;
        }
    }
    
    

    public function getSpeciesByAgent(?int $agentId = null): Collection
    {
        if (!$agentId) {
            return collect();
        }
    
        return DB::table('species')
            ->join('license_items', 'species.id', '=', 'license_items.species_id')
            ->join('licenses', 'license_items.license_id', '=', 'licenses.id')
            ->join('agents', 'agents.applicant_id', '=', 'licenses.applicant_id')
            ->where('agents.id', $agentId)
            ->select('species.id', 'species.name')
            ->distinct()
            ->get();
    }
    
}
