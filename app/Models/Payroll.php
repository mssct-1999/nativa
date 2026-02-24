<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Payroll
 * 
 * @property int $id
 * @property int $employee_id
 * @property Carbon $period_start
 * @property Carbon $period_end
 * @property float $gross
 * @property float $taxes
 * @property float $net
 * @property string $status
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Employee $employee
 *
 * @package App\Models
 */
class Payroll extends Model
{
	protected $table = 'payrolls';

	protected $casts = [
		'employee_id' => 'int',
		'period_start' => 'datetime',
		'period_end' => 'datetime',
		'gross' => 'float',
		'taxes' => 'float',
		'net' => 'float',
		'paid_at' => 'datetime'
	];

	protected $fillable = [
		'employee_id',
		'period_start',
		'period_end',
		'gross',
		'taxes',
		'net',
		'status',
		'paid_at'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}
}
