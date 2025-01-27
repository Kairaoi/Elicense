<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class SpeciesTrackingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks to avoid constraint issues during seeding
        Schema::disableForeignKeyConstraints();

        // Clear existing data
        DB::table('species_tracking')->truncate();

        // Sample data
        $data = [
            [
                'species_id' => 1,
                'agent_id' => 1,
                'island_id' => 1,
                'year' => 2023,
                'quota_allocated' => 100.00,
                'quota_used' => 20.00,
                'remaining_quota' => 80.00,
                'created_by' => User::inRandomOrder()->first()->id,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_id' => 2,
                'agent_id' => 2,
                'island_id' => 2,
                'year' => 2023,
                'quota_allocated' => 150.00,
                'quota_used' => 50.00,
                'remaining_quota' => 100.00,
                'created_by' => User::inRandomOrder()->first()->id,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_id' => 3,
                'agent_id' => 1,
                'island_id' => 3,
                'year' => 2024,
                'quota_allocated' => 200.00,
                'quota_used' => 0.00,
                'remaining_quota' => 200.00,
                'created_by' => User::inRandomOrder()->first()->id,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data
        DB::table('species_tracking')->insert($data);

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
}
