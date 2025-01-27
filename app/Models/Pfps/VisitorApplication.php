<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorApplication extends Model
{
    use HasFactory;

    protected $table = 'visitor_applications'; // Define the table name

    protected $primaryKey = 'application_id'; // Define the primary key column

    protected $fillable = [
        'visitor_id',
        'category_id',
        'activity_type_id',
        'duration_id',
        'status',
        'rejection_reason',
        'application_date',
        'created_by',
        'updated_by',
    ];

    
    // Define the relationship with the Visitor model
    public function visitor()
    {
        return $this->belongsTo(Visitor::class, 'visitor_id');
    }

    // Define the relationship with the PermitCategory model
    public function category()
    {
        return $this->belongsTo(PermitCategory::class, 'category_id', 'category_id');
    }

    // Define the relationship with the ActivityType model
    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    // Define the relationship with the Duration model
    public function duration()
    {
        return $this->belongsTo(Duration::class, 'duration_id');
    }

    // Define the relationship with the User model for created_by
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Define the relationship with the User model for updated_by
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Define the relationship with the Invoice model
    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'application_id');
    }

    // Define the relationship with the Permit model
    public function permit()
    {
        return $this->hasOne(Permit::class, 'application_id');
    }
    public function targetSpecies()
    {
        return $this->belongsToMany(TargetSpecies::class, 'application_target_species', 'application_id', 'species_id');
    }
}
