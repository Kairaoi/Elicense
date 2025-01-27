<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Pfps\Country;

class CountrySeeder extends Seeder
{
    public function run()
    {
       
    
        $countries = [
            [
                'country_name' => 'Kiribati',
                'iso_code' => 'KI',
                'created_by' => 1,
            ],
            [
                'country_name' => 'Australia',
                'iso_code' => 'AU',
                'created_by' => 1,
            ],
            [
                'country_name' => 'New Zealand',
                'iso_code' => 'NZ',
                'created_by' => 1,
            ],
            [
                'country_name' => 'Fiji',
                'iso_code' => 'FJ',
                'created_by' => 1,
            ],
            [
                'country_name' => 'Solomon Islands',
                'iso_code' => 'SB',
                'created_by' => 1,
            ],
            [
                'country_name' => 'United States',
                'iso_code' => 'US',
                'created_by' => 1,
            ],
            [
                'country_name' => 'Japan',
                'iso_code' => 'JP',
                'created_by' => 1,
            ],
            [
                'country_name' => 'China',
                'iso_code' => 'CN',
                'created_by' => 1,
            ],
            // Add more countries as needed...
        ];
    
        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
