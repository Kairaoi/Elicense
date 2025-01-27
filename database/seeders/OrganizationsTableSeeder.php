<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Schema::hasTable('organizations')) {
            DB::table('organizations')->insert([
                [
                    'organization_name' => 'Organization A',
                    'created_by' => 1,
                    'updated_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'organization_name' => 'Organization B',
                    'created_by' => 2,
                    'updated_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'organization_name' => 'Organization C',
                    'created_by' => 3,
                    'updated_by' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
