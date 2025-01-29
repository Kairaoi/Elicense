<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reference\Island;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LicenseItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'license_id',
        'species_id',
        'island_id', // Add this
        'requested_quota',
        'unit_price',
        'total_price',
        'created_by',
        'updated_by'
    ];

    // Define relationships
    public function license()
    {
        return $this->belongsTo(License::class); // Assuming you have a License model
    }

    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getRemainingQuota()
{
    return $this->requested_quota - MonthlyHarvest::where('license_item_id', $this->id)->sum('quantity_harvested');
}


}
