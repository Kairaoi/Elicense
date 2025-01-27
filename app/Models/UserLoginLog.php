<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLoginLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'login_at',
        'logout_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}