<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Quotation extends Model
{
	use HasFactory, SoftDeletes, CascadeSoftDeletes;

	protected $cascadeDeletes = ['items'];

	protected $fillable = [
		'client_id',
		'quotation_number',
		'emission_date',
		'expiration_date',
		'currency',
		'iva_rate',
		'subtotal',
		'discount_1',
		'discount_1_amount',
		'discount_2',
		'discount_2_amount',
		'freight',
		'tax_exempt',
		'tax_base',
		'iva_amount',
		'igtf_rate',
		'igtf_amount',
		'total',
		'notes',
		'status',
		'status_comment',
		'created_by'
	];

	protected $dates = ['emission_date', 'expiration_date'];

	/**
	 * A quotation belongs to a client.
	 */
	public function client()
	{
		return $this->belongsTo(Client::class);
	}

	/**
	 * A quotation was created by a user.
	 */
	public function author()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	/**
	 * A quotation has many product items.
	 */
	public function items()
	{
		return $this->hasMany(QuotationProduct::class)->orderBy('sort_order');
	}

	/**
	 * Get the formatted quotation number with leading zeros.
	 */
	public function getFormattedNumberAttribute()
	{
		return str_pad($this->quotation_number, 10, '0', STR_PAD_LEFT);
	}

	/**
	 * Generate the next correlative quotation number.
	 */
	public static function nextNumber()
	{
		$last = static::withTrashed()
			->orderByRaw('CAST(quotation_number AS UNSIGNED) DESC')
			->first();

		$next = $last ? (int) $last->quotation_number + 1 : 1;

		return str_pad($next, 10, '0', STR_PAD_LEFT);
	}
}
