<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\License\Species;
use App\Models\Reference\Island;

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
        
        $year = 2025; // Set current year

        foreach ($species as $speciesItem) {
            foreach ($islands as $island) {
                // Set the initial quota
                $island_quota = rand(100, 1000);
                
                // For new records, remaining quota equals the full quota
                $remaining_quota = $island_quota;
                
                // If you want to check existing usage and calculate actual remaining quota:
                // $used_quota = DB::table('your_usage_table')
                //     ->where('species_id', $speciesItem->id)
                //     ->where('island_id', $island->id)
                //     ->where('year', $year)
                //     ->sum('amount_used');
                // $remaining_quota = $island_quota - $used_quota;

                DB::table('species_island_quotas')->insert([
                    'species_id'      => $speciesItem->id,
                    'island_id'       => $island->id,
                    'island_quota'    => $island_quota,
                    'remaining_quota' => $remaining_quota,
                    'year'           => $year,
                    'created_by'      => $user->id,
                    'updated_by'      => $user->id,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }
    }
}