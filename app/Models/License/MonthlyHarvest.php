<?php

namespace App\Models\License;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Reference\Island;
class MonthlyHarvest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'license_item_id',
        'applicant_id',
        'island_id',
        'year',
        'month',
        'quantity_harvested',
        'remaining_quota',
        'notes',
        'created_by',
        'updated_by'
    ];

    public function licenseItem()
    {
        return $this->belongsTo(LicenseItem::class);
    }

    public function speciesTracking()
    {
        return $this->belongsTo(SpeciesTracking::class);
    }

     // Get species through license item
     public function species()
     {
         return $this->hasOneThrough(
             Species::class,
             LicenseItem::class,
             'id', // Foreign key on license_items table
             'id', // Foreign key on species table
             'license_item_id', // Local key on monthly_harvests table
             'species_id' // Local key on license_items table
         );
     }

     public function island()
     {
         return $this->belongsTo(Island::class);
     }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Helper method to get month name
    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

     // Define the relationship with the Agent model
    
}