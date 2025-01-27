<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\Lodge;
use App\Models\User;

class LodgeSeeder extends Seeder
{
    public function run()
    {
        // Seed multiple lodges
        $users = User::all(); // Assuming users are already in the database

        Lodge::create([
            'lodge_name' => 'Mountain Retreat',
            'location' => 'Highland Valley',
            'created_by' => $users->random()->id, // Random user from users table
            'updated_by' => $users->random()->id, // Optional, you can set to null if you prefer
        ]);

        Lodge::create([
            'lodge_name' => 'Seaside Escape',
            'location' => 'Coastal Bay',
            'created_by' => $users->random()->id,
            'updated_by' => $users->random()->id,
        ]);

        Lodge::create([
            'lodge_name' => 'Forest Haven',
            'location' => 'Greenwood Forest',
            'created_by' => $users->random()->id,
            'updated_by' => $users->random()->id,
        ]);
    }
}
