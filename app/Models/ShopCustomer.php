<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCustomer extends Model
{
    protected $table = 'shop_customers';

    protected $casts = [
        'user_id' => 'int',
        'fidelity_score' => 'int',
        'discount_rate' => 'float',
        'total_spent' => 'float',
        'orders_count' => 'int',
        'last_order_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'fidelity_score',
        'discount_rate',
        'total_spent',
        'orders_count',
        'last_order_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function registerOrder(float $amount): void
    {
        $points = (int) floor($amount / 10);
        $this->fidelity_score += max(0, $points);
        $this->orders_count += 1;
        $this->total_spent = (float) $this->total_spent + $amount;
        $this->last_order_at = now();
        $this->discount_rate = $this->calculateDiscountRate();
        $this->save();
    }

    public function calculateDiscountRate(): float
    {
        $raw = floor($this->fidelity_score / 100);
        $percent = min(15, max(0, $raw));

        return (float) $percent;
    }
}
