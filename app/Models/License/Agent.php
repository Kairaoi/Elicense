<?php

namespace App\Models\License;

use App\Models\User;
use App\Models\License\Applicant;
use App\Models\Reference\Island;
use App\Models\License\SpeciesTracking;
use App\Models\License\MonthlyHarvest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'agents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'applicant_id',
        'status',
        'start_date',
        'end_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the applicant associated with the agent.
     */
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the islands associated with the agent.
     */
    public function islands()
    {
        return $this->belongsToMany(Island::class, 'agent_island')
            ->withTimestamps()
            ->withPivot(['created_by', 'updated_by'])
            ->withTrashed();
    }

    /**
     * Get the species trackings for the agent.
     */
    public function speciesTrackings()
    {
        return $this->hasMany(SpeciesTracking::class);
    }

    /**
     * Get the user who created the agent.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the agent.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the agent's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the monthly harvests through species tracking.
     */
    public function monthlyHarvests()
    {
        return $this->hasManyThrough(MonthlyHarvest::class, SpeciesTracking::class);
    }

    /**
     * Scope a query to only include active agents.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive agents.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}