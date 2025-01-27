<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivitySite extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_sites'; // Define the table name

    protected $primaryKey = 'site_id'; // Define the primary key column

    protected $fillable = [
        'site_name',
        'category_id',
        'description',
        'location',
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
