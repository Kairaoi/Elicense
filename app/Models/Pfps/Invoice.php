<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices'; // Define the table name

    protected $primaryKey = 'invoice_id'; // Define the primary key column

    protected $fillable = [
        'application_id',
        'amount',
        'status',
        'invoice_date',
        'payment_reference',
        'created_by',
        'updated_by',
    ];

    // Define the relationship with the VisitorApplication model
    public function application()
    {
        return $this->belongsTo(VisitorApplication::class, 'application_id');
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

    // Define the relationship with the Permit model
    public function permit()
    {
        return $this->hasOne(Permit::class, 'invoice_id');
    }
}
