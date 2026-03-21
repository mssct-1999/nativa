<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProduct extends Model
{
    protected $table = 'shop_products';

    protected $casts = [
        'shop_id' => 'int',
        'product_id' => 'int',
        'price_override' => 'float',
        'is_active' => 'bool',
    ];

    protected $fillable = [
        'shop_id',
        'product_id',
        'price_override',
        'is_active',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function effectivePrice(): float
    {
        if ($this->price_override !== null) {
            return (float) $this->price_override;
        }

        return $this->product ? (float) $this->product->price : 0.0;
    }
}
