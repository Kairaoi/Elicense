<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\VisitorApplication;
use App\Models\Pfps\Visitor;
use App\Models\Pfps\PermitCategory;
use App\Models\Pfps\ActivityType;
use App\Models\Pfps\Duration;
use App\Models\User;
use Carbon\Carbon;

class VisitorApplicationSeeder extends Seeder
{
    public function run()
    {
        // Insert sample data into related tables first (if not already present)
        
        // Ensure we have a visitor
        $visitor = Visitor::firstOrCreate([
            'visitor_id' => 1,
        ], [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            // Add other necessary visitor data
        ]);

        // Ensure we have a permit category
        $category = PermitCategory::firstOrCreate([
            'category_id' => 1,
        ], [
            'name' => 'Fishing',
            // Add other necessary category data
        ]);

        // Ensure we have an activity type
        $activityType = ActivityType::firstOrCreate([
            'activity_type_id' => 1,
        ], [
            'name' => 'Recreational Fishing',
            // Add other necessary activity type data
        ]);

        // Ensure we have a duration
        $duration = Duration::firstOrCreate([
            'duration_id' => 1,
        ], [
            'duration' => '1 Week',
            // Add other necessary duration data
        ]);

       
        
        // Insert a visitor application
        VisitorApplication::create([
            'visitor_id' => $visitor->visitor_id,
            'category_id' => $category->category_id,
            'activity_type_id' => $activityType->activity_type_id,
            'duration_id' => $duration->duration_id,
            'status' => 'pending',
            'rejection_reason' => null, // Optional, can be filled if status is 'rejected'
            'application_date' => Carbon::now(),
            'created_by' => 1,
            'updated_by' =>1,
        ]);

        // You can add more records in a similar fashion
    }
}
