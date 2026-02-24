<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class InvoiceItem
 * 
 * @property int $id
 * @property int $invoice_id
 * @property int|null $product_id
 * @property string|null $description
 * @property float $quantity
 * @property float $unit_price
 * @property float $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Invoice $invoice
 * @property Product|null $product
 *
 * @package App\Models
 */
class InvoiceItem extends Model
{
	protected $table = 'invoice_items';

	protected $casts = [
		'invoice_id' => 'int',
		'product_id' => 'int',
		'quantity' => 'float',
		'unit_price' => 'float',
		'total' => 'float'
	];

	protected $fillable = [
		'invoice_id',
		'product_id',
		'description',
		'quantity',
		'unit_price',
		'total'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function product()
	{
		return $this->belongsTo(Product::class);
	}
}
