<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderItem
 * 
 * @property int $id
 * @property int $order_id
 * @property int|null $product_id
 * @property string|null $description
 * @property float $quantity
 * @property float $unit_price
 * @property float $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Order $order
 * @property Product|null $product
 *
 * @package App\Models
 */
class OrderItem extends Model
{
	protected $table = 'order_items';

	protected $casts = [
		'order_id' => 'int',
		'product_id' => 'int',
		'quantity' => 'float',
		'unit_price' => 'float',
		'total' => 'float'
	];

	protected $fillable = [
		'order_id',
		'product_id',
		'description',
		'quantity',
		'unit_price',
		'total'
	];

	public function order()
	{
		return $this->belongsTo(Order::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
