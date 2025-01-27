<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class ReportGroup extends Model
{
    protected $fillable = [
        'name', 'description'
    ];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}