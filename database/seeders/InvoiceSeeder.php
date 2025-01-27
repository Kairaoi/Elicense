<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\Invoice;
use App\Models\Pfps\VisitorApplication;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        

        // Ensure we have a visitor application to link to the invoice
        $application = VisitorApplication::firstOrCreate(
            ['application_id' => 1],
            [
                'visitor_id' => 1,
                'category_id' => 1, // Assuming there's a category with ID 1
                'activity_type_id' => 1, // Assuming there's an activity type with ID 1
                'duration_id' => 1, // Assuming there's a duration with ID 1
                'status' => 'approved',
                'application_date' => Carbon::now(),
                'created_by' =>1,
                'updated_by' => 1,
            ]
        );

        // Create an invoice record
        Invoice::create([
            'application_id' => $application->application_id,  // Link to visitor application
            'amount' => 150.00,  // Example amount
            'currency' => 'USD',  // Example currency
            'status' => 'pending',  // Set the status (e.g., 'pending' or 'paid')
            'invoice_date' => Carbon::now(),  // Set the invoice date
            'payment_reference' => null,  // Example null payment reference (can be updated once payment is made)
            'created_by' =>1,  // User who created the invoice
            'updated_by' => 1,  // User who last updated the invoice
        ]);

        // You can create more invoices in a similar way if needed
    }
}
