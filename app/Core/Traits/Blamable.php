<?php

namespace App\Core\Traits;

trait Blamable
{
    public static function bootBlamable(): void
    {
        static::creating(function ($model) {
            $model->created_by = auth()->check() ? auth()->user()->username : 'system';
            $model->updated_by = auth()->check() ? auth()->user()->username : 'system';
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->check() ? auth()->user()->username : 'system';
        });
    }
}
