<?php

namespace App\Repositories;

use App\Models\Report;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function executeReport($reportId, $parameters = [])
    {
        try {
            $report = Report::findOrFail($reportId);
            $query = $report->query;

            // Verify parameters exist and are valid
            if (!empty($parameters)) {
                $bindings = [];
                foreach ($parameters as $key => $value) {
                    if (!empty($value)) {
                        $bindings[$key] = $value;
                    }
                }
                return DB::select($query, $bindings);
            }

            return DB::select($query);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getAllReports()
    {
        return Report::with('reportGroup')->get();
    }

    public function getReportsByGroup($groupId)
    {
        return Report::where('report_group_id', $groupId)->get();
    }
}