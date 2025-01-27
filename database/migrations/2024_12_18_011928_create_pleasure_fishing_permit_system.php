<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePleasureFishingPermitSystem extends Migration
{
    public function up()
    {
        // Countries Table
        Schema::create('countries', function (Blueprint $table) {
            $table->id('country_id');
            $table->string('country_name');
            $table->string('iso_code', 2); // 2-letter ISO code (ISO 3166-1 alpha-2)
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            $table->unique('iso_code');
        });
    
        // Organizations Table (Agents)
        Schema::create('organizations', function (Blueprint $table) {
            $table->id('organization_id');
            $table->string('organization_name');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Lodges Table
        Schema::create('lodges', function (Blueprint $table) {
            $table->id('lodge_id');
            $table->string('lodge_name');
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Permit Categories Table
        Schema::create('permit_categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name');
            $table->text('description')->nullable();
            $table->decimal('base_fee', 8, 2);
            $table->string('fee_currency', 3)->default('USD');
            $table->string('fee_period')->default('annual');
            $table->boolean('requires_certification')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Target Species Table
        Schema::create('target_species', function (Blueprint $table) {
            $table->id('species_id');
            $table->string('species_name');
            $table->string('species_category');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Activity Sites Table
        Schema::create('activity_sites', function (Blueprint $table) {
            $table->id('site_id');
            $table->string('site_name');
            $table->foreignId('category_id')->constrained('permit_categories', 'category_id');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Durations Table
        Schema::create('durations', function (Blueprint $table) {
            $table->id('duration_id');
            $table->string('duration_name');
            $table->decimal('initial_fee', 8, 2);
            $table->decimal('extension_fee', 8, 2)->nullable();
            $table->integer('duration_weeks');
            $table->boolean('is_extension')->default(false);

            // Foreign key constraint for category_id
            $table->foreignId('category_id')->constrained('permit_categories', 'category_id')
                ->onDelete('cascade'); // Added onDelete('cascade') to maintain data integrity

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
        
    
        // Activity Types Table
        Schema::create('activity_types', function (Blueprint $table) {
            $table->id('activity_type_id');
            $table->foreignId('category_id')->constrained('permit_categories', 'category_id');
            $table->string('activity_name');
            $table->text('requirements')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Visitors Table
        Schema::create('visitors', function (Blueprint $table) {
            $table->id('visitor_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->text('home_address')->nullable();
            $table->string('passport_number');
            $table->foreignId('country_id')->constrained('countries', 'country_id');
            $table->foreignId('organization_id')->nullable()->constrained('organizations', 'organization_id');
            $table->date('arrival_date');
            $table->date('departure_date');
            $table->foreignId('lodge_id')->constrained('lodges', 'lodge_id');
            $table->string('emergency_contact')->nullable();
            $table->string('certification_number')->nullable();
            $table->string('certification_type')->nullable();
            $table->date('certification_expiry')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Visitor Applications Table
        Schema::create('visitor_applications', function (Blueprint $table) {
            $table->id('application_id');
            $table->foreignId('visitor_id')->constrained('visitors', 'visitor_id');
            $table->foreignId('category_id')->constrained('permit_categories', 'category_id');
            $table->foreignId('activity_type_id')->constrained('activity_types', 'activity_type_id');
            $table->foreignId('duration_id')->constrained('durations', 'duration_id');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Set default value
            $table->text('rejection_reason')->nullable();
            $table->date('application_date');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
        
    
        // Application Target Species Table
        Schema::create('application_target_species', function (Blueprint $table) {
            $table->id('application_target_species_id');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('species_id');
            $table->foreign('application_id')->references('application_id')->on('visitor_applications')->cascadeOnDelete();
            $table->foreign('species_id')->references('species_id')->on('target_species')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Application Activity Sites Table
        Schema::create('application_activity_sites', function (Blueprint $table) {
            $table->id('application_activity_sites_id');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('site_id');
            $table->foreign('application_id')->references('application_id')->on('visitor_applications')->cascadeOnDelete();
            $table->foreign('site_id')->references('site_id')->on('activity_sites')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->foreignId('application_id')->constrained('visitor_applications', 'application_id');
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'paid']);
            $table->date('invoice_date');
            $table->string('payment_reference')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Permits Table
        Schema::create('permits', function (Blueprint $table) {
            $table->id('permit_id');
            $table->string('permit_number')->unique();
            $table->foreignId('application_id')->constrained('visitor_applications', 'application_id');
            $table->foreignId('invoice_id')->constrained('invoices', 'invoice_id');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->enum('permit_type', ['printed', 'e-copy', 'General', 'Special', 'Temporary']);
            $table->boolean('is_active')->default(true);
            $table->text('special_conditions')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Permit Extensions Table
        Schema::create('permit_extensions', function (Blueprint $table) {
            $table->id('extension_id');
            $table->foreignId('permit_id')->constrained('permits', 'permit_id');
            $table->date('original_expiry_date');
            $table->date('new_expiry_date');
            $table->decimal('extension_fee', 8, 2);
            $table->integer('extension_weeks');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Equipment Rentals Table
        Schema::create('equipment_rentals', function (Blueprint $table) {
            $table->id('rental_id');
            $table->foreignId('permit_id')->constrained('permits', 'permit_id');
            $table->string('equipment_type');
            $table->decimal('rental_fee', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('rental_date');
            $table->date('return_date');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    
        // Annual Trip Fees Table
        Schema::create('annual_trip_fees', function (Blueprint $table) {
            $table->id('fee_id');
            $table->foreignId('category_id')->constrained('permit_categories', 'category_id');
            $table->foreignId('island_id')->constrained('islands'); 
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->year('year');
            $table->date('effective_date');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unique(['year', 'category_id', 'island_id']);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('annual_trip_fees');
        Schema::dropIfExists('equipment_rentals');
        Schema::dropIfExists('permit_extensions');
        Schema::dropIfExists('permits');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('application_activity_sites');
        Schema::dropIfExists('application_target_species');
        Schema::dropIfExists('visitor_applications');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('activity_types');
        Schema::dropIfExists('durations');
        Schema::dropIfExists('activity_sites');
        Schema::dropIfExists('target_species');
        Schema::dropIfExists('permit_categories');
  
        Schema::dropIfExists('lodges');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('countries');
    }
}