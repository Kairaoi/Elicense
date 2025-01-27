<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetSpecies extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'target_species';

    // The primary key for the model.
    protected $primaryKey = 'species_id';

    // Indicates if the model should be timestamped.
    public $timestamps = true;

    // The attributes that are mass assignable.
    protected $fillable = [
        'species_name',
        'species_category',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user who created the target species.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the target species.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function visitorApplications()
    {
        return $this->belongsToMany(VisitorApplication::class, 'application_target_species', 'species_id', 'application_id');
    }
}
