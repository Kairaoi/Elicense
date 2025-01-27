<?php

namespace App\Models\License;

use App\Models\User;
use App\Models\Reference\Island;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpeciesTracking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'species_tracking';

    protected $fillable = [
        'species_id',
        'agent_id',
        'island_id',
        'year',
        'quota_allocated',
        'quota_used',
        'remaining_quota',
        'created_by',
        'updated_by'
    ];

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    public function monthlyHarvests()
    {
        return $this->hasMany(MonthlyHarvest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper method to update quotas
    public function updateQuotas()
    {
        $this->quota_used = $this->monthlyHarvests->sum('quantity_harvested');
        $this->remaining_quota = $this->quota_allocated - $this->quota_used;
        $this->save();
    }
}