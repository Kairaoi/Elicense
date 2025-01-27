<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;

class Lodge extends Model
{
    protected $primaryKey = 'lodge_id';
    protected $fillable = ['lodge_name', 'location','created_by', 'updated_by'];

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'lodge_id');
    }
}