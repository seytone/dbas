<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SaleProduct extends Model
{
    use HasFactory, SoftDeletes;

	protected $table = 'sales_products';

	protected $fillable = [
		'sale_id',
		'product_id',
		'quantity',
		'cost',
		'price',
		'discount',
		'mercadolibre',
		'total'
	];

	/**
	 * A sale product belongs to a sale.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function sale()
	{
		return $this->belongsTo(Sale::class);
	}

	/**
	 * A sale product belongs to a product.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function product()
	{
		return $this->belongsTo(Product::class)->withTrashed();
	}
}
