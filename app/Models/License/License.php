<?php

namespace App\Models\License;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
class License extends Model
{
    use LogsActivity,HasFactory, SoftDeletes;

    protected $casts = [
        'invoice_date' => 'datetime',
    ];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'applicant_id',
                'license_type_id',
                'total_fee',
                'license_number',
                'issue_date',
                'expiry_date',
                'issued_by',
                'invoice_number',
                'invoice_date',
                'payment_date',
                'vat_amount',
                'total_amount_with_vat',
                'status',
                'created_by',
                'updated_by',
            ])
            ->logOnlyDirty() // Log only changed attributes
            ->setDescriptionForEvent(function (string $eventName) {
                return match ($eventName) {
                    'created' => 'License has been created',
                    'updated' => 'License has been updated',
                    'deleted' => 'License has been deleted',
                    default => "License has been {$eventName}",
                };
            })
            ->useLogName('license')
            ->dontSubmitEmptyLogs()
            ->logExcept(['created_at', 'updated_at', 'deleted_at'])
            ->dontLogIfAttributesChangedOnly(['updated_at']);
    }
    
    protected $fillable = [
        'applicant_id',
        'license_type_id',
        'total_fee',
        'license_number',
        'issue_date',
        'expiry_date',
        'revocation_reason',
        'revocation_date',  // Add this
        'revoked_by',      // Add this
        'issued_by',
        'invoice_number',
        'invoice_date',
        'payment_date',
        'vat_amount',
        'total_amount_with_vat',
        'created_by',
        'updated_by',
        'status',
    ];
    

    // Define relationships

    public function applicant()
    {
        return $this->belongsTo(Applicant::class);
    }

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function expiredBy()
    {
        return $this->belongsTo(User::class, 'expired_by');
    }
    public function licenseItems()
{
    return $this->hasMany(LicenseItem::class);  // Assuming LicenseItem is the related model
}



    protected static function booted()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    public function getAmountInWords()
{
    $amount = $this->total_amount_with_vat;
    $whole = floor($amount);
    $fraction = round(($amount - $whole) * 100);
    
    $dictionary = [
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 
        5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 
        14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
        18 => 'eighteen', 19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
        40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy',
        80 => 'eighty', 90 => 'ninety'
    ];

    $convertThreeDigits = function($number) use ($dictionary) {
        if ($number == 0) return '';

        $hundreds = floor($number / 100);
        $remainder = $number % 100;

        $words = '';

        if ($hundreds > 0) {
            $words .= $dictionary[$hundreds] . ' hundred ';
        }

        if ($remainder > 0) {
            if ($remainder < 20) {
                $words .= $dictionary[$remainder];
            } else {
                $tens = floor($remainder / 10) * 10;
                $ones = $remainder % 10;
                $words .= $dictionary[$tens];
                if ($ones > 0) {
                    $words .= '-' . $dictionary[$ones];
                }
            }
        }

        return $words;
    };

    $result = '';
    
    if ($whole == 0) {
        $result = 'zero';
    } else {
        $billions = floor($whole / 1000000000);
        $millions = floor(($whole % 1000000000) / 1000000);
        $thousands = floor(($whole % 1000000) / 1000);
        $remainder = $whole % 1000;

        if ($billions > 0) {
            $result .= $convertThreeDigits($billions) . ' billion ';
        }
        if ($millions > 0) {
            $result .= $convertThreeDigits($millions) . ' million ';
        }
        if ($thousands > 0) {
            $result .= $convertThreeDigits($thousands) . ' thousand ';
        }
        if ($remainder > 0) {
            $result .= $convertThreeDigits($remainder);
        }
    }

    if ($fraction > 0) {
        $result .= ' and ' . $fraction . '/100';
    }

    return ucfirst(trim($result));
}
}