<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFisheryLicensingSystemTables extends Migration
{
    public function up()
    {

        

        // 1. Applicants Table
        // Applicants Table
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->unique();
            $table->string('last_name')->unique();
            $table->string('company_name');
            $table->string('local_registration_number');
            $table->enum('types_of_company', ['Corporation', 'Partnership', 'Single Private Company']);
            $table->string('date_of_establishment');
            $table->string('citizenship');
            $table->string('work_address');
            $table->string('registered_address');
            $table->string('foreign_investment_license')->nullable();
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });


        // 2. License Types Table
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // 3. Species Table (references license_types)
        Schema::create('species', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('license_type_id')->constrained(); // References license_types
            $table->decimal('quota', 10, 2);
            $table->integer('year');
            $table->decimal('unit_price', 10, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // 4. Licenses Table (references applicants and license_types)
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained(); // References applicants
            $table->foreignId('license_type_id')->constrained(); // References license_types
            $table->decimal('total_fee', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'reviewed', 'license_issued', 'license_revoked', 'license_expired'])->default('pending');
            $table->text('revocation_reason')->nullable();
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('total_amount_with_vat', 10, 2)->default(0);
            $table->string('license_number')->unique()->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users');
            $table->date('revocation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('issued_by')->nullable()->constrained('users');
            $table->date('issue_date')->nullable();
            $table->foreignId('expired_by')->nullable()->constrained('users');
            $table->date('expiry_date')->nullable();
        });

         // 6. Islands Table
         Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // 5. License Items Table (references licenses and species)
        Schema::create('license_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained(); // References licenses
            $table->foreignId('species_id')->constrained(); // References species
            $table->foreignId('island_id')->constrained();
            $table->decimal('requested_quota', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

       

        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email')->unique();
             $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

      
        Schema::create('agent_island', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->onDelete('cascade');
            $table->foreignId('island_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->unique(['agent_id', 'island_id']);
        });

        Schema::create('species_island_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained(); // Assuming you have a species table
            $table->foreignId('island_id')->constrained(); // Assuming you have an island table
            $table->decimal('island_quota', 10, 2)->default(0); // For storing the quota for the island
            $table->decimal('remaining_quota', 10, 2)->default(0); // For remaining quota
            $table->integer('year'); // Year field
            $table->foreignId('created_by')->constrained('users'); // Created by user
            $table->foreignId('updated_by')->nullable()->constrained('users'); // Updated by user
            $table->softDeletes(); // For soft deletes
            $table->timestamps(); // For created_at and updated_at

            // Adding unique constraint if needed (e.g., species_id + island_id + year should be unique)
            $table->unique(['species_id', 'island_id', 'year']);
        });


        Schema::create('species_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_island_quota_id')->constrained(); // References 'id' in 'species_island_quotas'
            $table->foreignId('agent_id')->constrained();
            $table->decimal('quota_used', 10, 2)->default(0);
            $table->decimal('remaining_quota', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
        
     
        
  
        Schema::create('monthly_harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_item_id')->constrained('license_items')->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade'); 
            $table->foreignId('island_id')->constrained('islands')->onDelete('cascade');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('quantity_harvested', 10, 2);
            $table->decimal('remaining_quota', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            // $table->unique(['license_item_id', 'month', 'year'], 'monthly_harvest_unique');
        });
        
        
        Schema::create('harvester_applicants', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->boolean('is_group');
            $table->integer('group_size')->nullable();
            $table->string('national_id');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // 7. Harvester Licenses Table (references applicants and islands)
        Schema::create('harvester_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_number')->unique(); // Add this line
            $table->foreignId('harvester_applicant_id')->constrained('harvester_applicants');
            $table->foreignId('license_type_id')->constrained('license_types');
  
            $table->foreignId('island_id')->constrained();
            $table->decimal('fee', 10, 2);
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('payment_receipt_no');
            $table->enum('status', ['pending', 'reviewed', 'license_issued', 'license_revoked'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });


        // 8. Harvester License Species Table (references harvester_licenses and species)
        Schema::create('harvester_license_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harvester_license_id')->constrained(); // References harvester_licenses
            $table->foreignId('species_id')->constrained(); // References species
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });

        // 9. Group Members Table (references harvester_licenses)
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            // Create foreign key to the harvester_licenses table
            $table->foreignId('harvester_license_id')->constrained('harvester_licenses')->onDelete('cascade');
            $table->string('name');
            $table->string('national_id');
            $table->timestamps();
            $table->softDeletes();
            
            // User foreign key references for created_by and updated_by
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
        

        // 10. Export Declarations Table (references applicants)
        Schema::create('export_declarations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('applicants')->onDelete('cascade'); // References applicants
            $table->date('shipment_date');
            $table->string('export_destination');
            $table->decimal('total_license_fee', 10, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });

        // 11. Export Declaration Species Table (references export_declarations and species)
        Schema::create('export_declaration_species', function (Blueprint $table) {
            $table->id();
            $table->foreignId('export_declaration_id')->constrained('export_declarations')->onDelete('cascade'); // References export_declarations
            $table->foreignId('species_id')->constrained('species')->onDelete('cascade'); // References species
            $table->decimal('volume_kg', 10, 2);
            $table->decimal('under_size_volume_kg', 10, 2)->default(0);
            $table->decimal('fee_per_kg', 10, 2);
            $table->softDeletes();
            $table->timestamps();
        });

         // Species Quota History Table
         Schema::create('species_quota_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species');
            $table->decimal('previous_quota', 10, 2);
            $table->decimal('new_quota', 10, 2);
            $table->integer('year');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });

        // Island Quota History Table
        Schema::create('island_quota_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained('species');
            $table->foreignId('island_id')->constrained('islands');
            $table->decimal('previous_quota', 10, 2);
            $table->decimal('new_quota', 10, 2);
            $table->integer('year');
            $table->text('reason')->nullable();
            $table->timestamps();
            $table->foreignId('created_by')->constrained('users');
        });
    }

    public function down()
    {
        // Dropping tables in reverse order to prevent foreign key constraint issues
        Schema::dropIfExists('island_quota_histories');
        Schema::dropIfExists('species_quota_histories');
        Schema::dropIfExists('export_declaration_species');
        Schema::dropIfExists('export_declarations');
        Schema::dropIfExists('license_items');
        Schema::dropIfExists('licenses');
        Schema::dropIfExists('monthly_harvests');
        Schema::dropIfExists('species_tracking');
        Schema::dropIfExists('species_island_quotas');
        Schema::dropIfExists('agent_island');
        Schema::dropIfExists('agents');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('species');
        Schema::dropIfExists('license_types');
        Schema::dropIfExists('applicants');
        
    }
}
