<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class VisitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Example data for visitors
        $visitors = [
            [
                'first_name' => 'Bob',
                'last_name' => 'Johnson',
                'gender' => 'male',
                'home_address' => '789 Pine Ave, Village, Country',
                'passport_number' => 'C3456789',
                'country_id' => 3, // Another different existing country ID
                'organization_id' => null, // Can be null
                'arrival_date' => Carbon::parse('2024-03-05'),
                'departure_date' => Carbon::parse('2024-03-20'),
                'lodge_id' => 3, // Updated to a different existing lodge ID
                'emergency_contact' => '1122334455',
                'certification_number' => 'CERT98765',
                'certification_type' => 'Hiking Permit',
                'certification_expiry' => Carbon::parse('2025-06-01'),
                'created_by' => 3, // Updated to a different existing user ID
                'updated_by' => null, // Can be null
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'gender' => 'female',
                'home_address' => '456 Another St, City, Country',
                'passport_number' => 'B2345678',
                'country_id' => 2, // Assume this is an existing country ID
                'organization_id' => 1, // Assume this is an existing organization ID
                'arrival_date' => Carbon::parse('2024-02-10'),
                'departure_date' => Carbon::parse('2024-02-20'),
                'lodge_id' => 2, // Assume this is an existing lodge ID
                'emergency_contact' => '9876543211',
                'certification_number' => 'CERT67890',
                'certification_type' => 'Scuba Diver',
                'certification_expiry' => Carbon::parse('2026-02-10'),
                'created_by' => 1, // Assume this is an existing user ID
                'updated_by' => null, // Can be null
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert the data into the visitors table
        DB::table('visitors')->insert($visitors);
    }
}
