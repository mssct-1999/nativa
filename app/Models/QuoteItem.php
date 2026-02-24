<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QuoteItem
 * 
 * @property int $id
 * @property int $quote_id
 * @property int|null $product_id
 * @property string|null $description
 * @property float $quantity
 * @property float $unit_price
 * @property float $total
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Product|null $product
 * @property Quote $quote
 *
 * @package App\Models
 */
class QuoteItem extends Model
{
	protected $table = 'quote_items';

	protected $casts = [
		'quote_id' => 'int',
		'product_id' => 'int',
		'quantity' => 'float',
		'unit_price' => 'float',
		'total' => 'float'
	];

	protected $fillable = [
		'quote_id',
		'product_id',
		'description',
		'quantity',
		'unit_price',
		'total'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function quote()
	{
		return $this->belongsTo(Quote::class);
	}
}
