<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermitCategory extends Model
{
    use HasFactory;

    // Define the table name (optional, Laravel uses plural form of the model name by default)
    protected $table = 'permit_categories';

    // Define the primary key column (if it's different from the default 'id')
    protected $primaryKey = 'category_id';

    // Disable automatic management of timestamps if not needed
    public $timestamps = true;

    // Fillable fields to allow mass assignment
    protected $fillable = [
        'category_name',
        'description',
        'base_fee',
        'requires_certification',
        'created_by',
        'updated_by',
    ];

    // Relationships

    /**
     * Get the user that created the category.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user that last updated the category.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // You can add any custom methods here if needed
}
