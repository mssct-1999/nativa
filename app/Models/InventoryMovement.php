<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InventoryMovement
 * 
 * @property int $id
 * @property int $product_id
 * @property int|null $warehouse_from
 * @property int|null $warehouse_to
 * @property float $quantity
 * @property string $type
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int|null $user_id
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property User|null $user
 * @property Warehouse|null $warehouse
 *
 * @package App\Models
 */
class InventoryMovement extends Model
{
	protected $table = 'inventory_movements';

	protected $casts = [
		'product_id' => 'int',
		'warehouse_from' => 'int',
		'warehouse_to' => 'int',
		'quantity' => 'float',
		'reference_id' => 'int',
		'user_id' => 'int'
	];

	protected $fillable = [
		'product_id',
		'warehouse_from',
		'warehouse_to',
		'quantity',
		'type',
		'reference_type',
		'reference_id',
		'user_id',
		'notes'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function warehouse()
	{
		return $this->belongsTo(Warehouse::class, 'warehouse_to');
	}
}
