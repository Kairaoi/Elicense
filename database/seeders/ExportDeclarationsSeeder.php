<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExportDeclarationsSeeder extends Seeder
{
    public function run()
    {
        // Example data for export declarations
        $exportDeclarations = [
            [
                'applicant_id' => 1, // Adjust to match your applicants' IDs
                'shipment_date' => Carbon::now()->format('Y-m-d'),
                'export_destination' => 'Country A',
                'total_license_fee' => 1000.00,
                'created_by' => 1, // Adjust to match your users' IDs
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'applicant_id' => 2,
                'shipment_date' => Carbon::now()->format('Y-m-d'),
                'export_destination' => 'Country B',
                'total_license_fee' => 1500.00,
                'created_by' => 1,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more declarations as needed
        ];

        // Insert export declarations
        $declarationIds = DB::table('export_declarations')->insert($exportDeclarations);

        // Example data for export declaration species
        $exportDeclarationSpecies = [
            [
                'export_declaration_id' => 1, // Match with the export declaration ID
                'species_id' => 1, // Adjust to match your species' IDs
                'volume_kg' => 500.00,
                'under_size_volume_kg' => 50.00,
                'fee_per_kg' => 2.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'export_declaration_id' => 1,
                'species_id' => 2,
                'volume_kg' => 300.00,
                'under_size_volume_kg' => 30.00,
                'fee_per_kg' => 1.50,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'export_declaration_id' => 2,
                'species_id' => 1,
                'volume_kg' => 400.00,
                'under_size_volume_kg' => 40.00,
                'fee_per_kg' => 2.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Add more species entries as needed
        ];

        // Insert export declaration species
        DB::table('export_declaration_species')->insert($exportDeclarationSpecies);
    }
}
