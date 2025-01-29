<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\License\Species; // Assuming Species model exists
use App\Models\Reference\Island;  // Assuming Island model exists

class SpeciesIslandQuotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the first user for 'created_by' and 'updated_by'
        $user = User::first();

        // Fetch all species and islands from their respective models
        $species = Species::all();
        $islands = Island::all();

        foreach ($species as $speciesItem) {
            foreach ($islands as $island) {
                // Generate sample data for species-island quotas
                DB::table('species_island_quotas')->insert([
                    'species_id'      => $speciesItem->id,
                    'island_id'       => $island->id,
                    'island_quota'    => rand(100, 1000), // Random quota
                    'remaining_quota' => rand(50, 500),   // Random remaining quota
                    'year'           => 2025,  // Random year
                    'created_by'      => $user->id,        // Creator (first user)
                    'updated_by'      => $user->id,        // Updated by (first user)
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}
