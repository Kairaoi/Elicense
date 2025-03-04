<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        
        $year = 2025; // Set current year
        
        // Define quotas for each island and species
        $quotas = [
            // Island => [Surf redfish, Brown sandfish, Greenfish, Leopard fish, White teatfish, Black teatfish, Prickly redfish]
            'Makin' =>      [3143, 910, 1349, 95, 1989, 222, 1878],
            'Butaritari' => [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Marakei' =>    [3143, 910, 1349, 95, 1989, 222, 1878],
            'Abaiang' =>    [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Tarawa' =>     [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Maiana' =>     [3143, 910, 1349, 95, 1989, 222, 1878],
            'Kuria' =>      [3143, 910, 1349, 95, 1989, 222, 1878],
            'Aranuka' =>    [3143, 910, 1349, 95, 1989, 222, 1878],
            'Abemama' =>    [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Nonouti' =>    [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Tabiteuea' =>  [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Onotoa' =>     [3143, 910, 1349, 95, 1989, 222, 1878],
            'Beru' =>       [3143, 910, 1349, 95, 1989, 222, 1878],
            'Nikunau' =>    [3143, 910, 1349, 95, 1989, 222, 1878],
            'Tamana' =>     [3143, 910, 1349, 95, 1989, 222, 1878],
            'Arorae' =>     [3143, 910, 1349, 95, 1989, 222, 1878],
            'Kiritimati' => [6643, 3910, 2849, 1095, 3989, 472, 3878],
            'Tabuaeran' =>  [3143, 910, 1349, 95, 1989, 222, 1878],
            'Teraina' =>    [3143, 910, 1349, 95, 1989, 222, 1878],
        ];
        
        // Get species IDs for sea cucumber species (first 7 in the species table)
        $speciesIds = Species::where('license_type_id', 1)->orderBy('id')->take(7)->pluck('id')->toArray();

        // Define new species and their quotas for Kiritimati
        $newSpeciesWithQuotas = [
            'Flame Angel'   => 77848,
            'Sea Bass'      => 25376,
            'Black Tang'    => 624,
            'Gold Flake'     => 2171,
            'Lemon Peel'    => 6650,
            'Declivis'      => 648,
            'Emperor'       => 3000,
            'Griffis'       => 1500,
            'Gold Puffer'   => 210,
            'Other species' => 5000,
        ];

        // Get the species IDs for license type ID 2
        $speciesIds2 = Species::where('license_type_id', 2)
                             ->whereIn('name', array_keys($newSpeciesWithQuotas))
                             ->pluck('id', 'name')
                             ->toArray();

        // For each island
        foreach ($quotas as $islandName => $islandQuotas) {
            // Get the island ID
            $island = Island::where('name', $islandName)->first();
            
            if (!$island) {
                continue; // Skip if island not found
            }
            
            // For each species (license type 1)
            foreach ($speciesIds as $index => $speciesId) {
                if (isset($islandQuotas[$index])) {
                    $island_quota = $islandQuotas[$index];
                    // For new records, remaining quota equals the full quota
                    $remaining_quota = $island_quota;
                    
                    DB::table('species_island_quotas')->insert([
                        'species_id'      => $speciesId,
                        'island_id'       => $island->id,
                        'island_quota'    => $island_quota,
                        'remaining_quota' => $remaining_quota,
                        'year'            => $year,
                        'created_by'      => $user->id,
                        'updated_by'      => $user->id,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }

            // For the new species (license type 2), for Kiritimati
            if ($islandName === 'Kiritimati') {
                foreach ($newSpeciesWithQuotas as $speciesName => $islandQuota) {
                    if (isset($speciesIds2[$speciesName])) {
                        $speciesId = $speciesIds2[$speciesName];
                        $remainingQuota = $islandQuota;

                        // Debug output
                        Log::info("Inserting species: $speciesName with ID: $speciesId and quota: $remainingQuota");

                        DB::table('species_island_quotas')->insert([
                            'species_id'      => $speciesId,
                            'island_id'       => $island->id,
                            'island_quota'    => $islandQuota,
                            'remaining_quota' => $remainingQuota,
                            'year'            => $year,
                            'created_by'      => $user->id,
                            'updated_by'      => $user->id,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ]);
                    } else {
                        Log::info("Species not found: $speciesName");
                    }
                }
            }
        }
    }
}
