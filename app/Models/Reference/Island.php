<?php

namespace App\Models\Reference;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;


class Island extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'islands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the user that created the island.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that last updated the island.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Boot the model and assign default behaviors.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically assign the current authenticated user's ID when creating/updating
        static::creating(function ($island) {
            if (auth()->check()) {
                $island->created_by = auth()->id();
            }
        });

        static::updating(function ($island) {
            if (auth()->check()) {
                $island->updated_by = auth()->id();
            }
        });
    }
    public function agent()
{
    return $this->hasOne(Agent::class);  // Assuming each user has one agent
}

public function licenses()
{
    return $this->belongsToMany(License::class, 'island_license');
}
}
