<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Species extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'license_type_id',
        'quota',
        'year',
        'unit_price',
        'created_by',
        'updated_by',
    ];

    // Define relationships
    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class); // Assuming you have a LicenseType model
    }

    public function licenseItems()
    {
        return $this->hasMany(LicenseItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
