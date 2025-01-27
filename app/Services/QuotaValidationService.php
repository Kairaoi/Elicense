<?php

namespace App\Services;

use App\Models\License\SpeciesTracking;
use App\Models\License\IslandQuotaHistory;
use Illuminate\Support\Facades\DB;

class QuotaValidationService
{
    /**
     * Validate quota allocation against island limits
     *
     * @param int $speciesId
     * @param int $islandId
     * @param int $year
     * @param float $requestedQuota
     * @return array
     */
    public function validateQuotaAllocation($speciesId, $islandId, $year, $requestedQuota)
    {
        try {
            // Get the latest island quota for this species
            $latestQuota = IslandQuotaHistory::where('species_id', $speciesId)
                ->where('island_id', $islandId)
                ->where('year', $year)
                ->latest()
                ->first();

            if (!$latestQuota) {
                return [
                    'valid' => false,
                    'message' => 'No quota limit found for this species on this island.',
                    'available_quota' => 0
                ];
            }

            // Get total allocated quota for this species/island/year
            $totalAllocated = SpeciesTracking::where('species_id', $speciesId)
                ->where('island_id', $islandId)
                ->where('year', $year)
                ->sum('quota_allocated');

            $availableQuota = $latestQuota->new_quota - $totalAllocated;

            if ($requestedQuota > $availableQuota) {
                return [
                    'valid' => false,
                    'message' => "Requested quota exceeds available quota. Available: {$availableQuota}kg",
                    'available_quota' => $availableQuota
                ];
            }

            return [
                'valid' => true,
                'message' => 'Quota allocation is valid',
                'available_quota' => $availableQuota
            ];
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Error validating quota: ' . $e->getMessage(),
                'available_quota' => 0
            ];
        }
    }

    /**
     * Get quota summary for an island
     *
     * @param int $islandId
     * @param int $year
     * @return array
     */
    public function getIslandQuotaSummary($islandId, $year)
    {
        return DB::table('island_quota_histories as iqh')
            ->join('species as s', 's.id', '=', 'iqh.species_id')
            ->leftJoin('species_tracking as st', function ($join) use ($islandId, $year) {
                $join->on('st.species_id', '=', 'iqh.species_id')
                    ->where('st.island_id', '=', $islandId)
                    ->where('st.year', '=', $year);
            })
            ->where('iqh.island_id', $islandId)
            ->where('iqh.year', $year)
            ->groupBy('s.id', 's.name', 'iqh.new_quota')
            ->select(
                's.id',
                's.name',
                'iqh.new_quota as total_quota',
                DB::raw('COALESCE(SUM(st.quota_allocated), 0) as allocated_quota'),
                DB::raw('COALESCE(SUM(st.quota_used), 0) as used_quota'),
                DB::raw('iqh.new_quota - COALESCE(SUM(st.quota_allocated), 0) as remaining_quota')
            )
            ->get();
    }
}