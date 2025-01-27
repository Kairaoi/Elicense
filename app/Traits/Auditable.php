<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        \Log::info('Booting Auditable trait');
        // Creating event
        static::created(function ($model) {
            static::createAuditTrail($model, 'created');
        });

        // Updating event
        static::updated(function ($model) {
            static::createAuditTrail($model, 'updated');
        });

        // Deleting event
        static::deleted(function ($model) {
            static::createAuditTrail($model, 'deleted');
        });
    }

    protected static function createAuditTrail($model, $action)
    {
        try {
            AuditTrail::create([
                'table_name' => $model->getTable(),
                'record_id' => $model->id,
                'action' => $action,
                'old_values' => $action === 'created' ? null : json_encode($model->getOriginal()),
                'new_values' => $action === 'deleted' ? null : json_encode($model->getAttributes()),
                'user_id' => Auth::id(),
                'user_type' => Auth::user() ? get_class(Auth::user()) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            \Log::error('Audit Trail Error: ' . $e->getMessage());
        }
    }
}