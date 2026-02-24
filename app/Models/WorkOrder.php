<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WorkOrder
 * 
 * @property int $id
 * @property int|null $production_plan_id
 * @property string $order_number
 * @property int $product_id
 * @property float $quantity
 * @property string $status
 * @property Carbon|null $started_at
 * @property Carbon|null $completed_at
 * @property float|null $cost_estimate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product $product
 * @property ProductionPlan|null $production_plan
 * @property Collection|QualityCheck[] $quality_checks
 *
 * @package App\Models
 */
class WorkOrder extends Model
{
	protected $table = 'work_orders';

	protected $casts = [
		'production_plan_id' => 'int',
		'product_id' => 'int',
		'quantity' => 'float',
		'started_at' => 'datetime',
		'completed_at' => 'datetime',
		'cost_estimate' => 'float'
	];

	protected $fillable = [
		'production_plan_id',
		'order_number',
		'product_id',
		'quantity',
		'status',
		'started_at',
		'completed_at',
		'cost_estimate'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function production_plan()
	{
		return $this->belongsTo(ProductionPlan::class);
	}

	public function quality_checks()
	{
		return $this->hasMany(QualityCheck::class);
	}
}
