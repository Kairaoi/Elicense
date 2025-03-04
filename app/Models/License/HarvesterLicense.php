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

// In App\Models\License\HarvesterLicense.php
public static function generateLicenseNumber($licenseTypeId)
{
    // Get license type prefix
    $prefix = self::getLicenseTypePrefix($licenseTypeId);
    
    // Get current year
    $year = date('Y');
    
    // Find the highest sequence number for this license type and year
    $latestLicense = self::where('license_number', 'LIKE', $prefix . '-' . $year . '-%')
                         ->orderBy('license_number', 'desc')
                         ->first();
    
    $sequence = 1; // Default to 1 if no existing licenses
    
    if ($latestLicense) {
        // Extract the sequence number from the latest license
        $parts = explode('-', $latestLicense->license_number);
        $lastSequence = (int) end($parts);
        $sequence = $lastSequence + 1;
    }
    
    // Format with leading zeros (4 digits)
    $formattedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
    
    // Generate the new license number
    return $prefix . '-' . $year . '-' . $formattedSequence;
}

// Helper method to get license type prefix
private static function getLicenseTypePrefix($licenseTypeId)
{
    $prefixes = [
        1 => 'SC', // Sea cucumber
        2 => 'PF', // Pet fish
        3 => 'LB', // Lobster
        4 => 'SF', // Shark fin
        // Add more as needed
    ];
    
    return $prefixes[$licenseTypeId] ?? 'HL'; // Default to HL if no match
}
}
