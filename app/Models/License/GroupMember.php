<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMember extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'harvester_license_id',
        'name',
        'national_id',
        'created_by',
        'updated_by'
    ];

    public function harvesterLicense()
    {
        return $this->belongsTo(HarvesterLicense::class);
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