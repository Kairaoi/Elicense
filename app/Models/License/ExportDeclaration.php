<?php

namespace App\Models\License;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExportDeclaration extends Model
{
    use SoftDeletes;

    protected $table = 'export_declarations';

    protected $casts = [
        'shipment_date' => 'date',
    ];

    protected $fillable = [
        'applicant_id',
        'shipment_date',
        'export_destination',
        'total_license_fee',
        'created_by',
        'updated_by',
    ];

    // Relationships

    /**
     * Get the applicant associated with the export declaration.
     */
    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    /**
     * Get the user who created the export declaration.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the export declaration.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the species associated with the export declaration.
     */
    public function species()
    {
        return $this->hasMany(ExportDeclarationSpecies::class, 'export_declaration_id');
    }

    public function license()
{
    return $this->hasOne(License::class, 'applicant_id', 'applicant_id');
}

}
