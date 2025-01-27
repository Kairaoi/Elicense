<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentRental extends Model
{
    use SoftDeletes;

    // Define the table associated with the model
    protected $table = 'equipment_rentals';

    // Define the primary key
    protected $primaryKey = 'rental_id';

    // Define the fillable fields
    protected $fillable = [
        'permit_id',
        'equipment_type',
        'rental_fee',
        'currency',
        'rental_date',
        'return_date',
        'created_by',
        'updated_by'
    ];

    // Cast dates to Carbon instances for automatic date handling
    protected $dates = ['rental_date', 'return_date', 'created_at', 'updated_at', 'deleted_at'];

    // Define the relationship with the Permit model
    public function permit()
    {
        return $this->belongsTo(Permit::class, 'permit_id');
    }

    // Define the relationship with the User model for the 'created_by' field
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Define the relationship with the User model for the 'updated_by' field
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
