<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'query', 
        'parameters',
        'report_group_id'
    ];

    protected $casts = [
        'parameters' => 'array'  // Make sure parameters is cast to array
    ];

    // Add accessor to ensure parameters are never null and always array
    public function getParametersAttribute($value)
    {
        if (is_null($value)) return [];
        if (is_string($value)) return json_decode($value, true) ?: [];
        return (array) $value;
    }

    public function reportGroup()
    {
        return $this->belongsTo(ReportGroup::class);
    }
}