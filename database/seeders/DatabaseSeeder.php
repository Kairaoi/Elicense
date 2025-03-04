<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            FisheryLicensingSystemSeeder::class,
            ReportSeeder::class,
            CountrySeeder::class,
            SpeciesIslandQuotaSeeder::class,
            OrganizationsTableSeeder::class,
            LodgeSeeder::class,
            PermitCategoriesSeeder::class,
            DurationSeeder::class,
            ActivityTypesSeeder::class,
            TargetSpeciesSeeder::class,
            ActivitySiteSeeder::class,
            VisitorSeeder::class,
            VisitorApplicationSeeder::class,
            InvoiceSeeder::class,          // Move this BEFORE PermitSeeder
            PermitSeeder::class,           // This should come AFTER InvoiceSeeder
            EquipmentRentalSeeder::class,
            AnnualTripFeesTableSeeder::class,
            // SpeciesTrackingSeeder::class,

        ]);
    }
}