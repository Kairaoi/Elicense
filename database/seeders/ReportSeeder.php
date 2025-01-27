<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\ReportGroup;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run()
    {
        try {
            // Delete Reports first
            Report::whereHas('reportGroup', function ($query) {
                $query->where('name', 'Basic Reports');
            })->delete();

            // Delete the Report Group
            ReportGroup::where('name', 'Basic Reports')->delete();

            // Create Report Group
            $basicGroup = ReportGroup::create([
                'name' => 'Basic Reports',
                'description' => 'Simple database queries'
            ]);

            // Create Reports with the provided queries

            // Report 1 - License Status Summary
            Report::create([
                'name' => 'License Status Summary',
                'description' => 'Summary of licenses grouped by status.',
                'query' => "
                    SELECT 
                        status,
                        COUNT(*) as total_licenses,
                        SUM(total_fee) as total_fees
                    FROM licenses
                    GROUP BY status;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 2 - Export Declaration Species by Destination
            Report::create([
                'name' => 'Export Declaration Species by Destination',
                'description' => 'Species volumes and fees by export destination.',
                'query' => "
                    SELECT 
                        s.name as species_name,
                        ed.export_destination,
                        SUM(eds.volume_kg) as total_volume,
                        SUM(eds.volume_kg * eds.fee_per_kg) as total_value
                    FROM export_declaration_species eds
                    JOIN species s ON eds.species_id = s.id
                    JOIN export_declarations ed ON eds.export_declaration_id = ed.id
                    GROUP BY s.name, ed.export_destination;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 3 - Harvester License Summary by Island
            Report::create([
                'name' => 'Harvester License Summary by Island',
                'description' => 'Summary of harvester licenses by island.',
                'query' => "
                    SELECT 
                        i.name as island_name,
                        COUNT(hl.id) as total_licenses,
                        SUM(hl.fee) as total_fees
                    FROM harvester_licenses hl
                    JOIN islands i ON hl.island_id = i.id
                    GROUP BY i.name;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 4 - Applicants by Company Type
            Report::create([
                'name' => 'Applicants by Company Type',
                'description' => 'Total number of applicants grouped by company type.',
                'query' => "
                    SELECT 
                        types_of_company,
                        COUNT(*) as total_companies
                    FROM applicants
                    GROUP BY types_of_company;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 5 - Species Quota Usage
            Report::create([
                'name' => 'Species Quota Usage',
                'description' => 'Remaining and used quotas for each species.',
                'query' => "
                    SELECT 
                        s.name as species_name,
                        s.quota as total_quota,
                        SUM(li.requested_quota) as used_quota,
                        (s.quota - SUM(li.requested_quota)) as remaining_quota
                    FROM species s
                    LEFT JOIN license_items li ON s.id = li.species_id
                    GROUP BY s.id, s.name, s.quota;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 6 - Monthly License Revenue
            Report::create([
                'name' => 'Monthly License Revenue',
                'description' => 'Monthly revenue from licenses based on payment date.',
                'query' => "
                    SELECT 
                        DATE_FORMAT(payment_date, '%Y-%m') as month,
                        COUNT(*) as total_licenses,
                        SUM(total_fee) as total_revenue
                    FROM licenses
                    WHERE payment_date IS NOT NULL
                    GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
                    ORDER BY month;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 7 - Harvester Applicants by Group Type
            Report::create([
                'name' => 'Harvester Applicants by Group Type',
                'description' => 'Summary of harvester applicants by group type.',
                'query' => "
                    SELECT 
                        ha.is_group,
                        COUNT(*) as total_applicants,
                        AVG(ha.group_size) as average_group_size
                    FROM harvester_applicants ha
                    GROUP BY ha.is_group;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 8 - Species Undersized Volume
            Report::create([
                'name' => 'Species Undersized Volume',
                'description' => 'Undersized volume and average fee for each species.',
                'query' => "
                    SELECT 
                        s.name as species_name,
                        SUM(eds.volume_kg) as total_volume,
                        SUM(eds.under_size_volume_kg) as undersized_volume,
                        AVG(eds.fee_per_kg) as average_fee
                    FROM export_declaration_species eds
                    JOIN species s ON eds.species_id = s.id
                    GROUP BY s.name;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 9 - License Types Revenue
            Report::create([
                'name' => 'License Types Revenue',
                'description' => 'Revenue by license type.',
                'query' => "
                    SELECT 
                        lt.name as license_type,
                        COUNT(l.id) as total_licenses,
                        SUM(l.total_fee) as total_revenue
                    FROM license_types lt
                    LEFT JOIN licenses l ON lt.id = l.license_type_id
                    GROUP BY lt.id, lt.name;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 10 - Applicants by Citizenship
            Report::create([
                'name' => 'Applicants by Citizenship',
                'description' => 'Total applicants grouped by citizenship.',
                'query' => "
                    SELECT 
                        citizenship,
                        COUNT(*) as total_applicants
                    FROM applicants
                    GROUP BY citizenship;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 11 - License Processing Times
            Report::create([
                'name' => 'License Processing Times',
                'description' => 'Average, minimum, and maximum processing times for licenses.',
                'query' => "
                    SELECT 
                        AVG(DATEDIFF(issue_date, created_at)) as avg_processing_days,
                        MIN(DATEDIFF(issue_date, created_at)) as min_processing_days,
                        MAX(DATEDIFF(issue_date, created_at)) as max_processing_days
                    FROM licenses
                    WHERE issue_date IS NOT NULL;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            // Report 12 - Monthly Export Declarations
            Report::create([
                'name' => 'Monthly Export Declarations',
                'description' => 'Total export declarations and fees per month.',
                'query' => "
                    SELECT 
                        DATE_FORMAT(shipment_date, '%Y-%m') as month,
                        COUNT(*) as total_declarations,
                        SUM(total_license_fee) as total_fees
                    FROM export_declarations
                    GROUP BY DATE_FORMAT(shipment_date, '%Y-%m')
                    ORDER BY month;
                ",
                'parameters' => json_encode([]),
                'report_group_id' => $basicGroup->id
            ]);

            $this->command->info('Reports and Report Groups reseeded successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error reseeding Reports: ' . $e->getMessage());
        }
    }
}
