<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Client[] $clients
 * @property Collection|Employee[] $employees
 * @property Collection|InventoryMovement[] $inventory_movements
 * @property Collection|Invoice[] $invoices
 * @property Collection|Leaf[] $leaves
 * @property Collection|Order[] $orders
 * @property Collection|Prospect[] $prospects
 * @property Collection|Quote[] $quotes
 * @property Collection|Role[] $roles
 * @property Collection|SalesPipeline[] $sales_pipelines
 *
 * @package App\Models
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{

	use Authenticatable, Authorizable, CanResetPassword, HasApiTokens, HasFactory, Notifiable;
	protected $table = 'users';

	protected $casts = [
		'email_verified_at' => 'datetime'
	];

	protected $hidden = [
		'password',
		'remember_token'
	];

	protected $fillable = [
		'name',
		'email',
		'email_verified_at',
		'password',
		'remember_token'
	];

	public function clients()
	{
		return $this->hasMany(Client::class, 'created_by');
	}

	public function employees()
	{
		return $this->hasMany(Employee::class);
	}

	public function inventory_movements()
	{
		return $this->hasMany(InventoryMovement::class);
	}

	public function invoices()
	{
		return $this->hasMany(Invoice::class, 'created_by');
	}

	public function leaves()
	{
		return $this->hasMany(Leaf::class, 'approved_by');
	}

	public function orders()
	{
		return $this->hasMany(Order::class, 'created_by');
	}

	public function prospects()
	{
		return $this->hasMany(Prospect::class, 'assigned_to');
	}

	public function quotes()
	{
		return $this->hasMany(Quote::class, 'created_by');
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class)
					->withPivot('id')
					->withTimestamps();
	}

	public function sales_pipelines()
	{
		return $this->hasMany(SalesPipeline::class, 'assigned_to');
	}
}
