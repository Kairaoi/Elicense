<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Creating the report_groups table
        Schema::create('report_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Creating the reports table
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('query');
            $table->json('parameters')->nullable();
            $table->foreignId('report_group_id')->constrained();  // Foreign key linking to report_groups table
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dropping the reports table first because it has a foreign key dependency
        Schema::dropIfExists('reports');

        // Dropping the report_groups table
        Schema::dropIfExists('report_groups');
    }
};
