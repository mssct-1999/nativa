<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Product
 * 
 * @property int $id
 * @property string|null $sku
 * @property string $name
 * @property string|null $description
 * @property float $price
 * @property float $cost
 * @property bool $taxable
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Inventory[] $inventories
 * @property Collection|InventoryMovement[] $inventory_movements
 * @property Collection|InvoiceItem[] $invoice_items
 * @property Collection|OrderItem[] $order_items
 * @property Collection|ProductionPlan[] $production_plans
 * @property Collection|QuoteItem[] $quote_items
 * @property Collection|WorkOrder[] $work_orders
 *
 * @package App\Models
 */
class Product extends Model
{
	protected $table = 'products';

	protected $casts = [
		'price' => 'float',
		'cost' => 'float',
		'taxable' => 'bool',
		'active' => 'bool'
	];

	protected $fillable = [
		'sku',
		'name',
		'description',
		'price',
		'cost',
		'taxable',
		'active'
	];

	public function inventories()
	{
		return $this->hasMany(Inventory::class);
	}

	public function inventory_movements()
	{
		return $this->hasMany(InventoryMovement::class);
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function order_items()
	{
		return $this->hasMany(OrderItem::class);
	}

	public function production_plans()
	{
		return $this->hasMany(ProductionPlan::class);
	}

	public function quote_items()
	{
		return $this->hasMany(QuoteItem::class);
	}

	public function work_orders()
	{
		return $this->hasMany(WorkOrder::class);
	}
}
