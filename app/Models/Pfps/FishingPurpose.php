<?php

namespace App\Models\Pfps;

use Illuminate\Database\Eloquent\Model;

class FishingPurpose extends Model
{
    protected $primaryKey = 'purpose_id';
    protected $fillable = ['purpose_name'];

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'purpose_id');
    }
}