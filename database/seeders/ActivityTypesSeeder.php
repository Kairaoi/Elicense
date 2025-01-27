<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class ActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Sample categories, you should ensure that these IDs exist in your 'permit_categories' table
        $categories = DB::table('permit_categories')->pluck('category_id')->toArray();

        // Sample users (created_by and updated_by), ensure these IDs exist in your 'users' table
        $users = User::pluck('id')->toArray();

        // Insert example activity types
        DB::table('activity_types')->insert([
            [
                'category_id' => $categories[array_rand($categories)], // Random category ID
                'activity_name' => 'Fishing',
                'requirements' => 'Fishing license, appropriate gear, and safety precautions.',
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories[array_rand($categories)], // Random category ID
                'activity_name' => 'Diving',
                'requirements' => 'Diving certification, appropriate gear, and health screening.',
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => $categories[array_rand($categories)], // Random category ID
                'activity_name' => 'Flying',
                'requirements' => 'Pilot license, flight clearance, and weather conditions check.',
                'created_by' => $users[array_rand($users)], // Random user ID for created_by
                'updated_by' => $users[array_rand($users)], // Random user ID for updated_by
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
