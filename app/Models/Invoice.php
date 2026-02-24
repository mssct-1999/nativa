<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Invoice
 * 
 * @property int $id
 * @property string $number
 * @property int|null $client_id
 * @property int|null $quote_id
 * @property int|null $created_by
 * @property string $status
 * @property float $sub_total
 * @property float $tax
 * @property float $discount
 * @property float $total
 * @property Carbon|null $issued_at
 * @property Carbon|null $due_at
 * @property Carbon|null $paid_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Client|null $client
 * @property User|null $user
 * @property Quote|null $quote
 * @property Collection|InvoiceItem[] $invoice_items
 * @property Collection|Payment[] $payments
 *
 * @package App\Models
 */
class Invoice extends Model
{
	protected $table = 'invoices';

	protected $casts = [
		'client_id' => 'int',
		'quote_id' => 'int',
		'created_by' => 'int',
		'sub_total' => 'float',
		'tax' => 'float',
		'discount' => 'float',
		'total' => 'float',
		'issued_at' => 'datetime',
		'due_at' => 'datetime',
		'paid_at' => 'datetime'
	];

	protected $fillable = [
		'number',
		'client_id',
		'quote_id',
		'created_by',
		'status',
		'sub_total',
		'tax',
		'discount',
		'total',
		'issued_at',
		'due_at',
		'paid_at',
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

	public function quote()
	{
		return $this->belongsTo(Quote::class);
	}

	public function invoice_items()
	{
		return $this->hasMany(InvoiceItem::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}
}
