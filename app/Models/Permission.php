<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                $permission->slug = Str::slug($permission->name, '_', null);
            }
        });
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
