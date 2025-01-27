<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pfps\Permit;

class PermitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed sample data for the permits table
        Permit::insert([
            [
                'permit_number' => 'P123456',
                'application_id' => 1,
                'invoice_id' => 1,
                'issue_date' => now(),
                'expiry_date' => now()->addYear(),
                'permit_type' => 'General',
                'is_active' => true,
                'special_conditions' => 'None',
                'created_by' => 1,
                'updated_by' => null,
            ],
           
        ]);
    }
}
