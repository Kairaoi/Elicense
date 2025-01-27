<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    protected $primaryKey = 'duration_id';
    
    protected $fillable = [
        'duration_name',
        'initial_fee',
        'extension_fee',
        'duration_weeks',
        'is_extension',
        'category_id',
        'created_by',
        'updated_by',
    ];

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'duration_id');
    }
}