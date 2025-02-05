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

    public function getForDataTable($search = ''): Collection
{
    $query = $this->getModelInstance()->newQuery()->withTrashed()->with([
        'licenseItem.species:id,name',
        'applicant:id,first_name,last_name',
        'island:id,name',
    ]);

    if (!empty($search)) {
        $searchLower = strtolower($search);

        $query->where(function ($q) use ($searchLower) {
            $q->whereRaw('CAST(month AS TEXT) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('CAST(year AS TEXT) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('CAST(quantity_harvested AS TEXT) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(notes) LIKE ?', ['%' . $searchLower . '%']);
        });
    }

    return $query->orderByDesc('year')
        ->orderBy('month', 'asc')
        ->get();
}

    
}