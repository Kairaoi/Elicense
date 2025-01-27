<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditTrailTable extends Migration
{
    public function up()
    {
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('table_name'); // The table being audited
            $table->unsignedBigInteger('record_id'); // ID of the record being audited
            $table->string('action'); // Created, Updated, Deleted
            $table->text('old_values')->nullable(); // Previous values (JSON)
            $table->text('new_values')->nullable(); // New values (JSON)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['table_name', 'record_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_trails');
    }
}