<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Prospect
 * 
 * @property int $id
 * @property string|null $company_name
 * @property string|null $contact_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $source
 * @property string|null $notes
 * @property int|null $assigned_to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 *
 * @package App\Models
 */
class Prospect extends Model
{
	protected $table = 'prospects';

	protected $casts = [
		'assigned_to' => 'int'
	];

	protected $fillable = [
		'company_name',
		'contact_name',
		'email',
		'phone',
		'source',
		'notes',
		'assigned_to'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'assigned_to');
	}
}
