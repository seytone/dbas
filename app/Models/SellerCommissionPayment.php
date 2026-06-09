<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SellerCommissionPayment extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'seller_id',
		'year',
		'month',
		'total_commission',
		'commission_perpetual',
		'commission_annual',
		'commission_hardware',
		'commission_services',
		'payment',
		'payment_date',
		'payment_evidence',
		'comments',
	];

	protected $dates = ['payment_date'];

	public function seller()
	{
		return $this->belongsTo(Seller::class);
	}

	public function isPaid(): bool
	{
		return $this->payment === 'completed';
	}
}
