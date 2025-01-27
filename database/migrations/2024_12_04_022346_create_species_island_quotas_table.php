<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpeciesIslandQuotasTable extends Migration
{
    public function up()
    {
        Schema::create('species_island_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('species_id')->constrained();
            $table->foreignId('island_id')->constrained();
            $table->decimal('island_quota', 10, 2);
            $table->decimal('remaining_quota', 10, 2);
            $table->integer('year');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            // Ensure unique combination of species, island, and year
            $table->unique(['species_id', 'island_id', 'year']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('species_island_quotas');
    }
}
