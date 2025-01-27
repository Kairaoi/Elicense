<?php

namespace App\Models\License;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportDeclarationSpecies extends Model
{
    use SoftDeletes;

    protected $table = 'export_declaration_species';

    protected $fillable = [
        'export_declaration_id',
        'species_id',
        'volume_kg',
        'under_size_volume_kg',
        'fee_per_kg',
        'created_at',
        'updated_at',
    ];

    // Relationships

    /**
     * Get the export declaration associated with the species.
     */
    public function exportDeclaration()
    {
        return $this->belongsTo(ExportDeclaration::class, 'export_declaration_id');
    }

    /**
     * Get the species associated with this export declaration item.
     */
    public function species()
    {
        return $this->belongsTo(Species::class, 'species_id');
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function license()
    {
        return $this->belongsTo(HarvesterLicense::class, 'harvester_license_id');
    }
}
