<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class QualityCheck
 * 
 * @property int $id
 * @property int $work_order_id
 * @property int|null $inspector_id
 * @property string $result
 * @property string|null $notes
 * @property Carbon|null $checked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Employee|null $employee
 * @property WorkOrder $work_order
 *
 * @package App\Models
 */
class QualityCheck extends Model
{
	protected $table = 'quality_checks';

	protected $casts = [
		'work_order_id' => 'int',
		'inspector_id' => 'int',
		'checked_at' => 'datetime'
	];

	protected $fillable = [
		'work_order_id',
		'inspector_id',
		'result',
		'notes',
		'checked_at'
	];

	public function employee()
	{
		return $this->belongsTo(Employee::class, 'inspector_id');
	}

	public function work_order()
	{
		return $this->belongsTo(WorkOrder::class);
	}
}
