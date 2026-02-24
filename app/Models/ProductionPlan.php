<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductionPlan
 * 
 * @property int $id
 * @property string $name
 * @property int $product_id
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property float $quantity
 * @property string $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property Collection|WorkOrder[] $work_orders
 *
 * @package App\Models
 */
class ProductionPlan extends Model
{
	protected $table = 'production_plans';

	protected $casts = [
		'product_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'quantity' => 'float'
	];

	protected $fillable = [
		'name',
		'product_id',
		'start_date',
		'end_date',
		'quantity',
		'status',
		'notes'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function work_orders()
	{
		return $this->hasMany(WorkOrder::class);
	}
}
