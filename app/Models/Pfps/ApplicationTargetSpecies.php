<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationTargetSpecies extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'application_target_species';
    protected $primaryKey = 'application_target_species_id';
    protected $fillable = [
        'application_id',
        'species_id',
    ];

    /**
     * Get the associated application.
     */
    public function application()
    {
        return $this->belongsTo(VisitorApplication::class, 'application_id', 'application_id');
    }

    /**
     * Get the associated species.
     */
    public function species()
    {
        return $this->belongsTo(TargetSpecies::class, 'species_id', 'species_id');
    }
}
