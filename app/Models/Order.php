<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Order
 * 
 * @property int $id
 * @property string $number
 * @property int|null $client_id
 * @property int|null $created_by
 * @property string $status
 * @property float $total
 * @property Carbon|null $ordered_at
 * @property Carbon|null $shipped_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client|null $client
 * @property User|null $user
 * @property Collection|OrderItem[] $order_items
 *
 * @package App\Models
 */
class Order extends Model
{
	protected $table = 'orders';

	protected $casts = [
		'client_id' => 'int',
		'created_by' => 'int',
		'total' => 'float',
		'ordered_at' => 'datetime',
		'shipped_at' => 'datetime',
		'paid_at' => 'datetime'
	];

	protected $fillable = [
		'number',
		'client_id',
		'created_by',
		'status',
		'channel',
		'total',
		'ordered_at',
		'shipped_at',
		'paid_at',
		'payment_method',
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

	public function order_items()
	{
		return $this->hasMany(OrderItem::class);
	}
}
