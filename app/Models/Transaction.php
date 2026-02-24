<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Transaction
 * 
 * @property int $id
 * @property int $account_id
 * @property string $type
 * @property float $amount
 * @property Carbon|null $date
 * @property string|null $description
 * @property string $reference_type
 * @property int $reference_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Account $account
 * @property Collection|Payment[] $payments
 *
 * @package App\Models
 */
class Transaction extends Model
{
	protected $table = 'transactions';

	protected $casts = [
		'account_id' => 'int',
		'amount' => 'float',
		'date' => 'datetime',
		'reference_id' => 'int'
	];

	protected $fillable = [
		'account_id',
		'type',
		'amount',
		'date',
		'description',
		'reference_type',
		'reference_id'
	];

	public function account()
	{
		return $this->belongsTo(Account::class);
	}

	public function payments()
	{
		return $this->hasMany(Payment::class);
	}
}
