<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\License\SpeciesIslandQuota; // Assuming SpeciesIslandQuota model exists

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

        // Sample data with references to species_island_quotas
        $data = [
            [
                'species_island_quota_id' => SpeciesIslandQuota::where('species_id', 1)->where('island_id', 1)->where('year', 2025)->first()->id,
                'agent_id' => 1,
                'quota_used' => 20.00,
                'remaining_quota' => 80.00,
                'created_by' => User::inRandomOrder()->first()->id,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_island_quota_id' => SpeciesIslandQuota::where('species_id', 2)->where('island_id', 2)->where('year', 2025)->first()->id,
                'agent_id' => 2,
                'quota_used' => 50.00,
                'remaining_quota' => 100.00,
                'created_by' => User::inRandomOrder()->first()->id,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_island_quota_id' => SpeciesIslandQuota::where('species_id', 3)->where('island_id', 3)->where('year', 2025)->first()->id,
                'agent_id' => 1,
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
