<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

	protected static function booted()
	{
		static::creating(function (self $product) {
			if (empty($product->barcode)) {
				$product->barcode = self::generateBarcode();
			}
		});
	}

	public static function generateBarcode(): string
	{
		do {
			// Generate 12 digits: '2' + 11 random digits
			$barcode = '2'.str_pad((string) random_int(0, 99999999999), 11, '0', STR_PAD_LEFT);
			// Calculate EAN-13 checksum for the 12 digits
			$sum = 0;
			for ($i = 0; $i < 12; $i++) {
				$sum += (int)$barcode[$i] * ($i % 2 === 0 ? 1 : 3);
			}
			$checksum = (10 - ($sum % 10)) % 10;
			$barcode .= $checksum;
		} while (DB::table('products')->where('barcode', $barcode)->exists());

		return $barcode;
	}

	protected $casts = [
		'price' => 'float',
		'cost' => 'float',
		'taxable' => 'bool',
		'active' => 'bool'
	];

	protected $fillable = [
		'sku',
		'barcode',
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
