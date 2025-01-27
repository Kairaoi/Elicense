<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\Duration;

class DurationSeeder extends Seeder
{
    public function run()
    {
        // Example durations
        $durations = [
            [
                'duration_name' => '1 Day',  // Changed to duration_name
                'initial_fee' => 100.00,
                'extension_fee' => 50.00,
                'duration_weeks' => 0, // 1 day is less than a week
                'is_extension' => false,
                'category_id' => 1, // Replace with a valid category ID
                'created_by' => 1,  // Replace with a valid user ID
                'updated_by' => 1
            ],
            [
                'duration_name' => '3 Days', // Changed to duration_name
                'initial_fee' => 250.00,
                'extension_fee' => 75.00,
                'duration_weeks' => 0,
                'is_extension' => false,
                'category_id' => 1,
                'created_by' => 1,
                'updated_by' => 1
            ],
            [
                'duration_name' => '1 Week', // Changed to duration_name
                'initial_fee' => 400.00,
                'extension_fee' => 100.00,
                'duration_weeks' => 1,
                'is_extension' => false,
                'category_id' => 1,
                'created_by' => 1,
                'updated_by' => 1
            ]
        ];

        foreach ($durations as $duration) {
            // Use `firstOrCreate` to avoid duplicate entries
            Duration::firstOrCreate(
                ['duration_name' => $duration['duration_name']],  // Changed to duration_name
                $duration
            );
        }
    }
}
