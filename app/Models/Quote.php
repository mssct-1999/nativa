<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Quote
 * 
 * @property int $id
 * @property string $number
 * @property int|null $client_id
 * @property int|null $created_by
 * @property string $status
 * @property float $sub_total
 * @property float $tax
 * @property float $discount
 * @property float $total
 * @property Carbon|null $valid_until
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client|null $client
 * @property User|null $user
 * @property Collection|Invoice[] $invoices
 * @property Collection|QuoteItem[] $quote_items
 *
 * @package App\Models
 */
class Quote extends Model
{
	protected $table = 'quotes';

	protected $casts = [
		'client_id' => 'int',
		'created_by' => 'int',
		'sub_total' => 'float',
		'tax' => 'float',
		'discount' => 'float',
		'total' => 'float',
		'valid_until' => 'datetime'
	];

	protected $fillable = [
		'number',
		'client_id',
		'created_by',
		'status',
		'sub_total',
		'tax',
		'discount',
		'total',
		'valid_until',
		'notes'
	];

	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function quote_items()
	{
		return $this->hasMany(QuoteItem::class);
	}
}
