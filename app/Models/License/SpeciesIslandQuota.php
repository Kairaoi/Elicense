<?php

namespace App\Models\License;

use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Reference\Island;

class SpeciesIslandQuota extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'species_island_quotas';

    protected $fillable = [
        'species_id',
        'island_id',
        'island_quota',
        'remaining_quota',
        'year',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the species associated with the quota.
     */
    public function species()
    {
        return $this->belongsTo(Species::class); // Adjust if you have a Species model
    }

    /**
     * Get the island associated with the quota.
     */
    public function island()
    {
        return $this->belongsTo(Island::class); // Adjust if you have an Island model
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the formatted date for the created_at timestamp.
     */
    public function getCreatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * Get the formatted date for the updated_at timestamp.
     */
    public function getUpdatedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * Get the formatted date for the deleted_at timestamp.
     */
    public function getDeletedAtAttribute($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
    }
}
