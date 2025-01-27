<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Visitor extends Model
{
    use HasFactory;

    protected $table = 'visitors'; // Define the table name

    protected $primaryKey = 'visitor_id'; // Define the primary key column

    protected $fillable = [
        'first_name',
        'last_name',
        'passport_number',
        'country_id',
        'organization_id',
        'arrival_date',
        'departure_date',
        'lodge_id',
        'emergency_contact',
        'certification_number',
        'certification_type',
        'certification_expiry',
        'created_by',
        'updated_by',
    ];

    // Define the relationship with the Country model
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // Define the relationship with the Organization model
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    // Define the relationship with the Lodge model
    public function lodge()
    {
        return $this->belongsTo(Lodge::class, 'lodge_id');
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

    // Define the relationship with the VisitorApplication model
    public function applications()
    {
        return $this->hasMany(VisitorApplication::class, 'visitor_id');
    }
}
