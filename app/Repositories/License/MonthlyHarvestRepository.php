<?php

namespace App\Repositories\License;

use App\Models\License\MonthlyHarvest;
use App\Repositories\CustomBaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MonthlyHarvestRepository extends CustomBaseRepository
{
    public function model(): string
    {
        return MonthlyHarvest::class;
    }
    public function getForDataTable($search = '', $trackingId = null): Collection
{
    $query = $this->getModelInstance()->newQuery();

    if ($trackingId) {
        $query->where('species_tracking_id', $trackingId);
    }

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $searchLower = strtolower($search);
            $q->whereRaw('LOWER(month) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(quantity_harvested) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(notes) LIKE ?', ['%' . $searchLower . '%']);
        });
    }

    return $query->with([
        'speciesTracking.species:id,name',
        'agent:id,first_name,last_name', // Fetch agent name fields
        'creator:id,name',
        'updater:id,name'
    ])
    ->orderBy('month', 'asc')
    ->get();
}

    

}
