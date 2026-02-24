<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SalesPipeline
 * 
 * @property int $id
 * @property string $name
 * @property int|null $client_id
 * @property string $stage
 * @property float $value
 * @property int $probability
 * @property Carbon|null $expected_close
 * @property int|null $assigned_to
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Client|null $client
 *
 * @package App\Models
 */
class SalesPipeline extends Model
{
	protected $table = 'sales_pipelines';

	protected $casts = [
		'client_id' => 'int',
		'value' => 'float',
		'probability' => 'int',
		'expected_close' => 'datetime',
		'assigned_to' => 'int'
	];

	protected $fillable = [
		'name',
		'client_id',
		'stage',
		'value',
		'probability',
		'expected_close',
		'assigned_to',
		'notes'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'assigned_to');
	}

	public function client()
	{
		return $this->belongsTo(Client::class);
	}
}
