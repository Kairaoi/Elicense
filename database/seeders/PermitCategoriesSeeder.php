<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PermitCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample users (created_by and updated_by), ensure these IDs exist in your 'users' table
        $users = User::pluck('id')->toArray();

        // Fallback if there are no users
        if (empty($users)) {
            $users = [1];  // Fallback to a default user if no users exist
        }

        // Insert example permit categories
        DB::table('permit_categories')->insert([
            [
                'category_name' => 'Fishing',
                'description' => 'Allows for fishing in designated areas, with specific gear and licensing requirements.',
                'base_fee' => 50.00,
                'requires_certification' => true,
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Diving',
                'description' => 'Permit for scuba diving in various locations, requires certification and safety checks.',
                'base_fee' => 100.00,
                'requires_certification' => true,
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Flying',
                'description' => 'For flying light aircraft or recreational aircraft in specified zones, requires a pilot license.',
                'base_fee' => 200.00,
                'requires_certification' => true,
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_name' => 'Hiking',
                'description' => 'Permit for access to protected hiking trails and natural reserves.',
                'base_fee' => 30.00,
                'requires_certification' => false,
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
