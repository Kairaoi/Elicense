<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_types'; // Define the table name

    protected $primaryKey = 'activity_type_id'; // Define the primary key column

    protected $fillable = [
        'category_id',
        'activity_name',
        'requirements',
        'created_by',
        'updated_by',
    ];

    // Define the relationship with the PermitCategory model
    public function category()
    {
        return $this->belongsTo(PermitCategory::class, 'category_id', 'category_id');
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
}
