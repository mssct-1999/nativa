<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Inventory
 * 
 * @property int $id
 * @property int $product_id
 * @property int $warehouse_id
 * @property float $quantity
 * @property float $reserved
 * @property float $reorder_point
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property Warehouse $warehouse
 *
 * @package App\Models
 */
class Inventory extends Model
{
	protected $table = 'inventories';

	protected $casts = [
		'product_id' => 'int',
		'warehouse_id' => 'int',
		'quantity' => 'float',
		'reserved' => 'float',
		'reorder_point' => 'float'
	];

	protected $fillable = [
		'product_id',
		'warehouse_id',
		'quantity',
		'reserved',
		'reorder_point'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function warehouse()
	{
		return $this->belongsTo(Warehouse::class);
	}
}
