<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Leaf
 * 
 * @property int $id
 * @property int $employee_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string|null $type
 * @property string $status
 * @property string|null $notes
 * @property int|null $approved_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Employee $employee
 *
 * @package App\Models
 */
class Leaf extends Model
{
	protected $table = 'leaves';

	protected $casts = [
		'employee_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'approved_by' => 'int'
	];

	protected $fillable = [
		'employee_id',
		'start_date',
		'end_date',
		'type',
		'status',
		'notes',
		'approved_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'approved_by');
	}

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}
}
