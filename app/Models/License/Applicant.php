<?php

namespace App\Models\License;

use App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
// use App\Traits\Auditable;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Applicant extends Model
{
    use LogsActivity, HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'applicants';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'first_name',
                'last_name',
                'company_name',
                'local_registration_number',
                'types_of_company',
                'date_of_establishment',
                'citizenship',
                'work_address',
                'registered_address',
                'foreign_investment_license',
                'phone_number',
                'email',
                'status'
            ])
            ->logOnlyDirty() // Log only changed attributes
            ->setDescriptionForEvent(fn(string $eventName) => "Applicant has been {$eventName}")
            ->useLogName('applicant')
            ->dontSubmitEmptyLogs();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'local_registration_number',
        'types_of_company',
        'date_of_establishment',
        'citizenship',
        'work_address',
        'registered_address',
        'foreign_investment_license',
        'phone_number',
        'email',
        'created_by',
        'updated_by',
    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function company()
    {
        return $this->hasOne(Company::class, 'applicant_id');
    }

    protected static function booted()
{
    static::creating(function ($model) {
        // If authenticated, set the user ID, else leave it null
        if (Auth::check()) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        } else {
            $model->created_by = null;
            $model->updated_by = null;
        }
    });
}

public function licenses()
{
    return $this->hasMany(License::class);
}

public function user()
    {
        return $this->hasOne(User::class);
    }

}
