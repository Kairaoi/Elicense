<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualTripFee extends Model
{
    use HasFactory;

    protected $table = 'annual_trip_fees'; // Define the table name

    protected $primaryKey = 'fee_id'; // Define the primary key column

    protected $fillable = [
        'category_id',     // foreign key from permit_categories table
        'island_id',       // foreign key from islands table
        'amount',          // the amount of the fee
        'currency',        // currency of the fee (e.g., USD)
        'year',            // the year the fee is valid for
        'effective_date',  // date when the fee becomes effective
        'notes',           // optional notes about the fee
        'is_active',       // whether the fee is active
        'created_by',      // foreign key for the user who created the fee
        'updated_by',      // foreign key for the user who last updated the fee
    ];

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
}
