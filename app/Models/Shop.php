<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    protected $table = 'shops';

    protected $casts = [
        'owner_id' => 'int',
        'warehouse_id' => 'int',
        'is_active' => 'bool',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'warehouse_id',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function (self $shop) {
            if (! $shop->slug) {
                $shop->slug = self::generateUniqueSlug($shop->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $counter = 1;

        while (self::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function shop_products()
    {
        return $this->hasMany(ShopProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'shop_products')
            ->withPivot(['price_override', 'is_active'])
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
