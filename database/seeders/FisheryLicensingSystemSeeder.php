<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class FisheryLicensingSystemSeeder extends Seeder
{
    public function run()
    {
        // Seed Users (for created_by and updated_by fields)
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminUserId = DB::table('users')->where('email', 'admin@example.com')->value('id');

        // Seed Applicants
        // DB::table('applicants')->insert([
        //     [
        //         'first_name' => 'Joseph',
        //         'last_name' => 'Ioteba',
        //         'company_name' => 'Kiribati Fishing Co.',
        //         'local_registration_number' => 'REG123456',
        //         'types_of_company' => 'Corporation',
        //         'date_of_establishment' => '2020-01-01',
        //         'citizenship' => 'Kiribati',
        //         'work_address' => 'P.O. Box 123, Tarawa',
        //         'registered_address' => 'P.O. Box 123, Tarawa',
        //         'foreign_investment_license' => 'INV-001',
        //         'phone_number' => '+686 12345',
        //         'email' => 'josepht@mfmrd.gov.ki',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'created_by' => $adminUserId,
        //         'updated_by' => $adminUserId,
        //     ],
        //     [
        //         'first_name' => 'Toreeka',
        //         'last_name' => 'Temari',
        //         'company_name' => 'Betio Fishermen Association',
        //         'local_registration_number' => 'REG654321',
        //         'types_of_company' => 'Partnership',
        //         'date_of_establishment' => '2019-05-15',
        //         'citizenship' => 'Kiribati',
        //         'work_address' => 'Betio, Tarawa',
        //         'registered_address' => 'Betio, Tarawa',
        //         'foreign_investment_license' => 'INV-002',
        //         'phone_number' => '+686 67890',
        //         'email' => 'toorekat@mfmrd.gov.ki',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'created_by' => $adminUserId,
        //         'updated_by' => $adminUserId,
        //     ],
        // ]);

        // Seed License Types
        DB::table('license_types')->insert([
            [
                'name' => 'Export License for Seacucumber ',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Export License for Petfish',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Export License for Lobster',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Export License for Shark Fin',
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
        ]);

        // Seed Species
        DB::table('species')->insert([
            [
                'name' => 'Surf Redfish',
                'license_type_id' => 1,
                'quota' => 84222.00,
                'year' => 2025,
                'unit_price' => 3.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Brown Sandfish',
                'license_type_id' => 1,
                'quota' => 38286.00,
                'year' => 2025,
                'unit_price' => 3.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Greenfish',
                'license_type_id' => 1,
                'quota' => 36138.00,
                'year' => 2025,
                'unit_price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Leopard fish',
                'license_type_id' => 1,
                'quota' => 8814.00,
                'year' => 2025,
                'unit_price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'White teatfish',
                'license_type_id' => 1,
                'quota' => 51800.00,
                'year' => 2025,
                'unit_price' => 15.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Black teatfish',
                'license_type_id' => 1,
                'quota' => 5977.00,
                'year' => 2025,
                'unit_price' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Prickly teatfish',
                'license_type_id' => 1,
                'quota' => 49680.00,
                'year' => 2025,
                'unit_price' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Flame Angel',
                'license_type_id' => 2,
                'quota' => 77848.00,
                'year' => 2025,
                'unit_price' => 0.70,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Sea Bass',
                'license_type_id' => 2,
                'quota' => 25376.00,
                'year' => 2025,
                'unit_price' => 0.40,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Black Tang',
                'license_type_id' => 2,
                'quota' => 624.00,
                'year' => 2025,
                'unit_price' => 20.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Gold Flake',
                'license_type_id' => 2,
                'quota' => 2171.00,
                'year' => 2025,
                'unit_price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            
            [
                'name' => 'Lemon Peel',
                'license_type_id' => 2,
                'quota' => 6650.00,
                'year' => 2025,
                'unit_price' => 0.40,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Declivis',
                'license_type_id' => 2,
                'quota' => 648.00,
                'year' => 2025,
                'unit_price' => 6.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Emperor',
                'license_type_id' => 2,
                'quota' => 3000.00,
                'year' => 2025,
                'unit_price' => 6.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Griffis',
                'license_type_id' => 2,
                'quota' => 1500.00,
                'year' => 2025,
                'unit_price' => 10.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Gold Puffer',
                'license_type_id' => 2,
                'quota' => 210.00,
                'year' => 2025,
                'unit_price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Other species',
                'license_type_id' => 2,
                'quota' => 5000.00,
                'year' => 2025,
                'unit_price' => 1.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Lobster',
                'license_type_id' => 3,
                'quota' => 14165.00,
                'year' => 2025,
                'unit_price' => 5.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ],
            [
                'name' => 'Shark fin',
                'license_type_id' => 4,
                'quota' => 10000.00, 
                'year' => 2025, // Fix here, removing the comma
                'unit_price' => 130.00,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ]
            
        ]);

       

        $islands = [
            'Makin', 'Butaritari', 'Marakei', 'Abaiang', 'Tarawa', 'Maiana', 
            'Kuria', 'Aranuka', 'Abemama', 'Nonouti', 'Tabiteuea', 'Onotoa', 
            'Beru', 'Nikunau', 'Tamana', 'Arorae', 'Kiritimati', 'Tabuaeran', 'Teraina'
        ];

        foreach ($islands as $islandName) {
            DB::table('islands')->insert([
                'name' => $islandName,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
            ]);

        }

        // // Seed Licenses
        // DB::table('licenses')->insert([
        //     [
        //         'applicant_id' => 1,
        //         'license_type_id' => 1,
        //         'total_fee' => 5000.00,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'created_by' => $adminUserId,
        //         'updated_by' => $adminUserId,
        //     ],
        // ]);

        // // Seed License Items
        // DB::table('license_items')->insert([
        //     [
        //         'license_id' => 1,
        //         'species_id' => 1,
        //         'requested_quota' => 1000.00,
        //         'unit_price' => 5.00,
        //         'total_price' => 5000.00,
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //         'created_by' => $adminUserId,
        //         'updated_by' => $adminUserId,
        //     ],
        // ]);
    //     DB::table('harvester_applicants')->insert([
    //         [
    //             'first_name' => 'Alice',
    //             'last_name' => 'Smith',
    //             'phone_number' => '1234567890',
    //             'email' => 'alice.smith@example.com',
    //             'is_group' => false,
    //             'group_size' => null,
    //             'national_id' => 'NID-001',
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //         [
    //             'first_name' => 'Bob',
    //             'last_name' => 'Johnson',
    //             'phone_number' => '0987654321',
    //             'email' => 'bob.johnson@example.com',
    //             'is_group' => true,
    //             'group_size' => 5,
    //             'national_id' => 'NID-002',
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //         [
    //             'first_name' => 'Charlie',
    //             'last_name' => 'Brown',
    //             'phone_number' => '5551234567',
    //             'email' => 'charlie.brown@example.com',
    //             'is_group' => false,
    //             'group_size' => null,
    //             'national_id' => 'NID-003',
    //             'created_at' => Carbon::now(),
    //             'updated_at' => Carbon::now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //     ]);
    //     // Seed Harvester Licenses
    //     DB::table('harvester_licenses')->insert([
    //         [
    //             'license_number' => 1,
    //             'harvester_applicant_id' => 1, // Ensure this ID exists in the harvester_applicants table
    //             'island_id' => 1, // Ensure this ID exists in the islands table
    //             'license_type_id' => 1,
                
    //             'fee' => 25.00,
    //             'issue_date' => Carbon::now(),
    //             'expiry_date' => Carbon::now()->addYear(),
    //             'payment_receipt_no' => 'REC-001',
                
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //     ]);

    //     // Seed Harvester License Species
    //     DB::table('harvester_license_species')->insert([
    //         [
                
    //             'harvester_license_id' => 1,
    //             'species_id' => 2,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //     ]);

    //     // Seed Group Members
    //     DB::table('group_members')->insert([
    //         [
    //             'harvester_license_id' => 1,
    //             'name' => 'Tebware Tangata',
    //             'national_id' => 'KIR-67890',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //         [
    //             'harvester_license_id' => 1,
    //             'name' => 'Taalua Bauro',
    //             'national_id' => 'KIR-13579',
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //             'created_by' => $adminUserId,
    //             'updated_by' => $adminUserId,
    //         ],
    //     ]);
     }
}