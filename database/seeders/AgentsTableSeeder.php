<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agents = [];

        for ($i = 1; $i <= 10; $i++) {
            $agents[] = [
                'first_name' => 'FirstName' . $i,
                'last_name' => 'LastName' . $i,
                'phone_number' => '123456789' . $i,
                'email' => 'agent' . $i . '@example.com',
                'applicant_id' => rand(1, 5), // Assuming applicants exist in the database
                'status' => $i % 2 == 0 ? 'active' : 'inactive',
                'start_date' => now()->subDays(rand(0, 365))->format('Y-m-d'),
                'end_date' => $i % 3 == 0 ? now()->addDays(rand(1, 365))->format('Y-m-d') : null,
                'notes' => 'This is a note for agent ' . $i,
                'created_by' => 1, // Assuming a user with ID 1 exists
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }

        DB::table('agents')->insert($agents);
    }
}
