<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 * 
 * @property int $id
 * @property string $name
 * @property string|null $type
 * @property string|null $currency
 * @property float $balance
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Transaction[] $transactions
 *
 * @package App\Models
 */
class Account extends Model
{
	protected $table = 'accounts';

	protected $casts = [
		'balance' => 'float'
	];

	protected $fillable = [
		'name',
		'type',
		'currency',
		'balance',
		'notes'
	];

	public function transactions()
	{
		return $this->hasMany(Transaction::class);
	}
}
