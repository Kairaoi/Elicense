<?php

namespace App\Models\License;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyHarvest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'species_tracking_id',
        'agent_id',
        'island_id',
        'year',
        'month',
        'quantity_harvested',
        'notes',
        'created_by',
        'updated_by'
    ];

    public function speciesTracking()
    {
        return $this->belongsTo(SpeciesTracking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper method to get month name
    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

     // Define the relationship with the Agent model
     public function agent()
     {
         return $this->belongsTo(Agent::class);
     }
}