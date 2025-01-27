<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // Primary Key
    protected $primaryKey = 'country_id';

    // Fillable fields for mass assignment
    protected $fillable = [
        'country_name',
        'iso_code',
        'created_by',
        'updated_by'
    ];

    // Relationship with the Visitor model
    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'country_id');
    }

    // Relationship with the User model for 'created_by'
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relationship with the User model for 'updated_by'
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
