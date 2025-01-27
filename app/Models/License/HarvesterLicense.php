<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Reference\Island; 

class HarvesterLicense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'license_number',
        'harvester_applicant_id',
        'license_type_id',
        'island_id',
        'fee',
        'issue_date',
        'expiry_date',
        'payment_receipt_no',
        'status',
        'created_by',
        'updated_by',
    ];

    // Optionally, define the casts for specific fields if needed
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'fee' => 'decimal:2',
    ];
    /**
     * Get the harvester applicant associated with the license.
     */
    public function applicant()
    {
        return $this->belongsTo(HarvesterApplicant::class, 'harvester_applicant_id');
    }

    /**
     * Get the island associated with the license.
     */
    public function island()
    {
        return $this->belongsTo(Island::class);
    }

    /**
     * Get the user who created the license.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the license.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    

    public function species()
    {
        return $this->belongsToMany(Species::class, 'harvester_license_species');
    }

    public function licenseType()
{
    return $this->belongsTo(LicenseType::class, 'license_type_id');
}
public function getDisplayLicenseTypeName()
{
    $originalName = $this->licenseType->name;
    return str_replace('Export License for', 'Harvest License for', $originalName);
}

public static function generateLicenseNumber($licenseTypeId)
{
    try {
        // Get the current year
        $year = date('Y');
        
        // Get the license type prefix
        $prefixMap = [
            1 => 'SC', // Seacucumber
            2 => 'PF', // Petfish
            3 => 'LB', // Lobster
            4 => 'SF'  // Shark Fin
        ];
        
        $prefix = $prefixMap[$licenseTypeId] ?? 'HL';

        // Get the last license number for this type and year
        $lastLicense = self::where('license_number', 'like', $prefix . $year . '%')
            ->orderBy('license_number', 'desc')
            ->first();

        if ($lastLicense) {
            // Extract the sequence number and increment
            $sequence = intval(substr($lastLicense->license_number, -4)) + 1;
        } else {
            $sequence = 1;
        }

        // Format: PREFIX-YEAR-SEQUENCE (e.g., SC-2024-0001)
        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    } catch (\Exception $e) {
        \Log::error('Error generating license number: ' . $e->getMessage());
        throw $e;
    }
}

}
