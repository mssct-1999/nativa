<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Employee
 * 
 * @property int $id
 * @property int|null $user_id
 * @property string|null $employee_number
 * @property Carbon|null $hire_date
 * @property string|null $position
 * @property string|null $department
 * @property float|null $salary
 * @property int|null $manager_id
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Employee|null $employee
 * @property User|null $user
 * @property Collection|Employee[] $employees
 * @property Collection|Leaf[] $leaves
 * @property Collection|Payroll[] $payrolls
 * @property Collection|QualityCheck[] $quality_checks
 * @property Collection|TimeEntry[] $time_entries
 *
 * @package App\Models
 */
class Employee extends Model
{
	protected $table = 'employees';

	protected $casts = [
		'user_id' => 'int',
		'hire_date' => 'datetime',
		'salary' => 'float',
		'manager_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'employee_number',
		'hire_date',
		'position',
		'department',
		'salary',
		'manager_id',
		'status'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'manager_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function employees()
	{
		return $this->hasMany(Employee::class, 'manager_id');
	}

	public function leaves()
	{
		return $this->hasMany(Leaf::class);
	}

	public function payrolls()
	{
		return $this->hasMany(Payroll::class);
	}

	public function quality_checks()
	{
		return $this->hasMany(QualityCheck::class, 'inspector_id');
	}

	public function time_entries()
	{
		return $this->hasMany(TimeEntry::class);
	}
}
