<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Modify the users table to add foreign key for applicant_id
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'applicant_id')) {
                $table->unsignedBigInteger('applicant_id')->nullable();
            }

            // Add foreign key only if not exists
            if (!Schema::hasColumn('users', 'applicant_id')) {
                $table->foreign('applicant_id')
                      ->references('id')
                      ->on('applicants')
                      ->onDelete('cascade');
            }
        });

        // Modify the applicants table to add foreign keys for created_by and updated_by
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
            }
            if (!Schema::hasColumn('applicants', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable();
            }

            // Add foreign keys only if not exists
            if (!Schema::hasColumn('applicants', 'created_by')) {
                $table->foreign('created_by')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            }
            if (!Schema::hasColumn('applicants', 'updated_by')) {
                $table->foreign('updated_by')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        // Drop foreign keys and columns in reverse order
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'applicant_id')) {
                $table->dropForeign(['applicant_id']);
                $table->dropColumn('applicant_id');
            }
        });

        Schema::table('applicants', function (Blueprint $table) {
            if (Schema::hasColumn('applicants', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('applicants', 'updated_by')) {
                $table->dropForeign(['updated_by']);
                $table->dropColumn('updated_by');
            }
        });
    }
};
