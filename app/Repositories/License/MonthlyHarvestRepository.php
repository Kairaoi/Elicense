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

    public function getForDataTable($search = '', $filters = []): Collection
{
    \Log::info('Repository Filter Parameters:', [
        'search' => $search,
        'filters' => $filters
    ]);

    $query = $this->getModelInstance()->newQuery()
        ->withTrashed()
        ->with([
            'licenseItem.species:id,name',
            'applicant:id,first_name,last_name',
            'island:id,name',
        ]);

    // Apply search
    if (!empty($search)) {
        $searchLower = strtolower($search);
        $query->where(function ($q) use ($searchLower) {
            $q->whereRaw('CAST(month AS TEXT) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('CAST(year AS TEXT) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('CAST(quantity_harvested AS TEXT) LIKE ?', ['%' . $searchLower . '%'])
              ->orWhereRaw('LOWER(notes) LIKE ?', ['%' . $searchLower . '%']);
        });
    }

    // Apply filters with logging
    if (!empty($filters['island'])) {
        \Log::info('Applying Island Filter:', ['island' => $filters['island']]);
        $query->whereHas('island', function($q) use ($filters) {
            $q->where('name', $filters['island']);
        });
    }

    if (!empty($filters['month'])) {
        \Log::info('Applying Month Filter:', ['month' => $filters['month']]);
        $query->where('month', $filters['month']);
    }

    if (!empty($filters['species'])) {
        \Log::info('Applying Species Filter:', ['species' => $filters['species']]);
        $query->whereHas('licenseItem.species', function($q) use ($filters) {
            $q->where('name', $filters['species']);
        });
    }

    if (!empty($filters['applicant'])) {
        \Log::info('Applying Applicant Filter:', ['applicant' => $filters['applicant']]);
        $query->whereHas('applicant', function($q) use ($filters) {
            $q->where('first_name', 'like', '%' . $filters['applicant'] . '%')
              ->orWhere('last_name', 'like', '%' . $filters['applicant'] . '%');
        });
    }

    $results = $query->orderByDesc('year')
        ->orderBy('month', 'asc')
        ->get();

    \Log::info('Final Query Results:', [
        'count' => $results->count(),
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings()
    ]);

    return $results;
}

    
}