<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\ActivitySite;
use App\Models\Pfps\PermitCategory;
use App\Models\User;

class ActivitySiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Example data for ActivitySite seeder

        // Fetch some sample Permit Categories and Users for reference
        $category = PermitCategory::first(); // Ensure at least one permit category exists
        $user = User::first(); // Ensure at least one user exists

        // If there are no categories or users, exit the seeder
        if (!$category || !$user) {
            $this->command->info('No Permit Category or User found in the database.');
            return;
        }

        // Create some activity sites
        ActivitySite::create([
            'site_name' => 'Fishing Spot 1',
            'category_id' => $category->category_id,  // Use first Permit Category
            'description' => 'Great fishing spot with lots of fish.',
            'location' => 'North Lake',
            'created_by' => $user->id,  // Use first User
            'updated_by' => $user->id,
        ]);

        ActivitySite::create([
            'site_name' => 'Fishing Spot 2',
            'category_id' => $category->category_id,  // Use first Permit Category
            'description' => 'A calm and relaxing fishing area.',
            'location' => 'South River',
            'created_by' => $user->id,  // Use first User
            'updated_by' => $user->id,
        ]);

        ActivitySite::create([
            'site_name' => 'Fishing Spot 3',
            'category_id' => $category->category_id,  // Use first Permit Category
            'description' => 'A remote location with great catches.',
            'location' => 'West Bay',
            'created_by' => $user->id,  // Use first User
            'updated_by' => $user->id,
        ]);

        $this->command->info('Activity sites seeded successfully!');
    }
}
