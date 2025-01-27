<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\EquipmentRental;
use App\Models\Pfps\Permit; // Ensure this points to your correct Permit model
use App\Models\Pfps\VisitorApplication; // Ensure this points to your correct VisitorApplication model
use Carbon\Carbon;
use DB;

class EquipmentRentalSeeder extends Seeder
{
    public function run()
    {
        // Insert a visitor application
        $visitorApplication = VisitorApplication::create([
            'visitor_id' => 1, // Make sure this visitor_id exists in the visitors table
            'category_id' => 1, // Make sure this category_id exists in the permit_categories table
            'activity_type_id' => 1, // Make sure this activity_type_id exists in the activity_types table
            'duration_id' => 1, // Make sure this duration_id exists in the durations table
            'status' => 'approved',
            'application_date' => Carbon::now(),
            'created_by' => 1, // Ensure this created_by user exists in the users table
            'updated_by' => 1, // Ensure this updated_by user exists in the users table
        ]);

        // Insert permit data
        $permits = [
            [
                'permit_number' => 'PERMIT-001',
                'application_id' => $visitorApplication->application_id, // Reference the visitor_application we just created
                'invoice_id' => 1, // Ensure this invoice_id exists in the invoices table
                'issue_date' => Carbon::now(),
                'expiry_date' => Carbon::now()->addYear(),
                'permit_type' => 'printed',
                'is_active' => true,
                'special_conditions' => null,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            // Other permit records...
        ];

        foreach ($permits as $permitData) {
            Permit::create($permitData);
        }

        // Sample data for equipment rentals
        $rentals = [
            [
                'permit_id' => 1, // Ensure this permit_id exists in the permits table
                'equipment_type' => 'Boat',
                'rental_fee' => 100.00,
                'currency' => 'USD',
                'rental_date' => Carbon::now(),
                'return_date' => Carbon::now()->addDays(5),
                'created_by' => 1, // Ensure this created_by user exists in the users table
                'updated_by' => 1, // Ensure this updated_by user exists in the users table
            ],
            // Other rental records...
        ];

        foreach ($rentals as $rental) {
            EquipmentRental::create($rental);
        }
    }
}
