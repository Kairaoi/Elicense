<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $primaryKey = 'organization_id';
    protected $fillable = ['organization_name','created_by',
        'updated_by'
    ];

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'organization_id');
    }
}