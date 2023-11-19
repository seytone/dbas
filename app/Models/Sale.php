<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = ['client_id', 'seller_id', 'invoice_type', 'invoice_number', 'payment_method', 'payment_currency', 'payment_amount_usd', 'payment_amount_bsf', 'subtotal', 'iva', 'igtf', 'cityhall', 'total', 'provider', 'profit', 'commission', 'commission_perpetual', 'commission_annual', 'commission_hardware', 'commission_services', 'trello', 'notes', 'registered_at'];

	/**
	 * The attributes that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['registered_at'];

	/**
	 * The User that belong to the seller.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function seller()
	{
		return $this->belongsTo(Seller::class);
	}

	/**
	 * A sale belongs to a client.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	/**
	 * A sale has many products.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function products()
	{
		return $this->hasMany(SaleProduct::class);
	}

	/**
	 * The sale has many services.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function services()
	{
		return $this->hasMany(SaleService::class);
	}
}