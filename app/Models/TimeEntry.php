<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class TimeEntry
 * 
 * @property int $id
 * @property int $employee_id
 * @property Carbon $date
 * @property float $hours
 * @property string|null $project
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Employee $employee
 *
 * @package App\Models
 */
class TimeEntry extends Model
{
	protected $table = 'time_entries';

	protected $casts = [
		'employee_id' => 'int',
		'date' => 'datetime',
		'hours' => 'float'
	];

	protected $fillable = [
		'employee_id',
		'date',
		'hours',
		'project',
		'description'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}
}
