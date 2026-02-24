<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Warehouse
 * 
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property string|null $location
 * @property string|null $contact
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Inventory[] $inventories
 * @property Collection|InventoryMovement[] $inventory_movements
 *
 * @package App\Models
 */
class Warehouse extends Model
{
	protected $table = 'warehouses';

	protected $fillable = [
		'name',
		'code',
		'location',
		'contact',
		'notes'
	];

	public function inventories()
	{
		return $this->hasMany(Inventory::class);
	}

	public function inventory_movements()
	{
		return $this->hasMany(InventoryMovement::class, 'warehouse_to');
	}
}
