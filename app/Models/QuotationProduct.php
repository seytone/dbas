<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class QuotationProduct extends Model
{
	use HasFactory, SoftDeletes;

	protected $table = 'quotation_products';

	protected $fillable = [
		'quotation_id',
		'product_id',
		'code',
		'description',
		'quantity',
		'unit_price',
		'discount_percent',
		'discount_amount',
		'total',
		'sort_order'
	];

	/**
	 * A quotation product belongs to a quotation.
	 */
	public function quotation()
	{
		return $this->belongsTo(Quotation::class);
	}

	/**
	 * A quotation product may belong to a registered product.
	 */
	public function product()
	{
		return $this->belongsTo(Product::class)->withTrashed();
	}

	/**
	 * Check if this is a free (unregistered) product.
	 */
	public function getIsFreeProductAttribute()
	{
		return is_null($this->product_id);
	}
}
