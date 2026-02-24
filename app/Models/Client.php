<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
 * 
 * @property int $id
 * @property string $company_name
 * @property string|null $contact_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $zipcode
 * @property string|null $country
 * @property string|null $notes
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property User|null $user
 * @property Collection|Invoice[] $invoices
 * @property Collection|Order[] $orders
 * @property Collection|Quote[] $quotes
 * @property Collection|SalesPipeline[] $sales_pipelines
 *
 * @package App\Models
 */
class Client extends Model
{
	protected $table = 'clients';

	protected $casts = [
		'created_by' => 'int'
	];

	protected $fillable = [
		'company_name',
		'contact_name',
		'email',
		'phone',
		'address',
		'city',
		'zipcode',
		'country',
		'notes',
		'created_by'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class);
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}

	public function quotes()
	{
		return $this->hasMany(Quote::class);
	}

	public function sales_pipelines()
	{
		return $this->hasMany(SalesPipeline::class);
	}
}
