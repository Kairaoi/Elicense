<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema; // Import Schema class
use Carbon\Carbon;

class AnnualTripFeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if the table exists before seeding
        if (!Schema::hasTable('annual_trip_fees')) {
            $this->command->error('The annual_trip_fees table does not exist.');
            return;
        }

        // Seed data
        $fees = [
            [
                'category_id' => 1, // Ensure this corresponds to a valid category_id from permit_categories
                'island_id' => 1, // Ensure this corresponds to a valid island_id from islands
                'amount' => 250.00,
                'currency' => 'USD',
                'year' => 2023,
                'effective_date' => Carbon::now(),
                'notes' => 'Fee for 2023',
                'is_active' => true,
                'created_by' => 1, // Ensure this corresponds to a valid user ID
                'updated_by' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category_id' => 1, // Ensure this corresponds to a valid category_id from permit_categories
                'island_id' => 1, // Ensure this corresponds to a valid island_id from islands
                'amount' => 275.00,
                'currency' => 'USD',
                'year' => 2024,
                'effective_date' => Carbon::now(),
                'notes' => 'Fee for 2024',
                'is_active' => true,
                'created_by' => 1, // Ensure this corresponds to a valid user ID
                'updated_by' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Insert data into the table
        DB::table('annual_trip_fees')->insert($fees);
    }
}
