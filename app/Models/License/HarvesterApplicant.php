<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HarvesterApplicant extends Model
{
    use HasFactory, SoftDeletes;

    // Specify the table name if it's not the plural of the model name
    protected $table = 'harvester_applicants';

    // Define fillable attributes
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'is_group',
        'group_size',
        'national_id',
        'created_by',
        'updated_by',
    ];

    // Define hidden attributes (if any)
    protected $hidden = [
        'created_by',
        'updated_by',
    ];

    // Define relationships if necessary
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
