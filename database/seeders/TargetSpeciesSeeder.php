<?php

namespace Database\Seeders;

use App\Models\Pfps\TargetSpecies;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TargetSpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample data for target species
        $targetSpecies = [
            [
                'species_name' => 'Trigger fish',
                'species_category' => 'Inshore Pelagic',
                'description' => 'A small, aggressive fish known for its sharp teeth.',
                'created_by' => 1, // Assuming user with ID 1 exists
                'updated_by' => 1, // Assuming user with ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_name' => 'Bonefish',
                'species_category' => 'Inshore Pelagic',
                'description' => 'A fast and elusive fish that is a prized catch for fly fishermen.',
                'created_by' => 1, // Assuming user with ID 1 exists
                'updated_by' => 1, // Assuming user with ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_name' => 'Tarpon',
                'species_category' => 'Coastal',
                'description' => 'Known for their size and strength, often caught on sport fishing trips.',
                'created_by' => 1, // Assuming user with ID 1 exists
                'updated_by' => 1, // Assuming user with ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_name' => 'Snook',
                'species_category' => 'Estuarine',
                'description' => 'A popular game fish, commonly found in shallow coastal waters.',
                'created_by' => 1, // Assuming user with ID 1 exists
                'updated_by' => 1, // Assuming user with ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'species_name' => 'Grouper',
                'species_category' => 'Reef',
                'description' => 'A large fish that is a popular target for recreational fishermen.',
                'created_by' => 1, // Assuming user with ID 1 exists
                'updated_by' => 1, // Assuming user with ID 1 exists
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert data into the target_species table
        DB::table('target_species')->insert($targetSpecies);

        // Alternatively, you can use the model directly
        // TargetSpecies::insert($targetSpecies);
    }
}
