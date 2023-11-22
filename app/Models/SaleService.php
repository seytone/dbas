<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleService extends Model
{
    use HasFactory, SoftDeletes;

	protected $table = 'sales_services';
	
	protected $fillable = [
		'sale_id',
		'service_id',
		'quantity',
		'price',
		'discount',
		'total'
	];

	/**
	 * A sale service belongs to a sale.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function sale()
	{
		return $this->belongsTo(Sale::class);
	}

	/**
	 * A sale service belongs to a service.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function service()
	{
		return $this->belongsTo(Service::class);
	}
}
