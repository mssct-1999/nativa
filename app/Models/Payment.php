<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payment
 * 
 * @property int $id
 * @property int|null $invoice_id
 * @property int|null $transaction_id
 * @property float $amount
 * @property string|null $method
 * @property string|null $reference
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Invoice|null $invoice
 * @property Transaction|null $transaction
 *
 * @package App\Models
 */
class Payment extends Model
{
	protected $table = 'payments';

	protected $casts = [
		'invoice_id' => 'int',
		'transaction_id' => 'int',
		'amount' => 'float',
		'paid_at' => 'datetime'
	];

	protected $fillable = [
		'invoice_id',
		'transaction_id',
		'amount',
		'method',
		'reference',
		'paid_at'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}

	public function transaction()
	{
		return $this->belongsTo(Transaction::class);
	}
}
