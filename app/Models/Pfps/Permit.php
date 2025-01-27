<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    protected $table = 'permits'; // Define the table name

    protected $primaryKey = 'permit_id'; // Define the primary key column

    protected $fillable = [
        'permit_number',
        'application_id',
        'invoice_id',
        'issue_date',
        'expiry_date',
        'permit_type',
        'is_active',
        'special_conditions',
        'created_by',
        'updated_by',
    ];

    // Define the relationship with the VisitorApplication model
    public function application()
    {
        return $this->belongsTo(VisitorApplication::class, 'application_id');
    }

    // Define the relationship with the Invoice model
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
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

    // Define the relationship with the EquipmentRental model
    public function equipmentRentals()
    {
        return $this->hasMany(EquipmentRental::class, 'permit_id');
    }
}
