<?php

namespace App\Models;

use Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    /** @use HasFactory<MenuItemFactory> */
    use HasFactory;
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'image_url', 'is_available'
    ];
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name, '_', null);
            }
        });
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            function ($value) {
                if (!$value) {
                    return null;
                }
                 return Storage::disk('public')->url($value);
            }
        );
    }
}
