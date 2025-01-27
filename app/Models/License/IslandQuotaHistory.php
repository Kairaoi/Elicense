<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Reference\Island;
use App\Models\Reference\Species;

class IslandQuotaHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'island_quota_histories';

    protected $fillable = [
        'species_id',
        'island_id',
        'previous_quota',
        'new_quota',
        'year',
        'reason',
        'created_by',
    ];

    /**
     * Get the species associated with the quota history.
     */
    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the island associated with the quota history.
     */
    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    /**
     * Get the user who created the record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
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
