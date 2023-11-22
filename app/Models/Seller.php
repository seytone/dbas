<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\User;

class Seller extends Model
{
	use HasFactory, SoftDeletes, CascadeSoftDeletes;

	protected $cascadeDeletes = ['sales'];

	protected $fillable = [
		'user_id',
		'commission_1',
		'commission_2',
		'commission_3',
		'commission_4'
	];

	/**
	 * The attributes that are guarded.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];

	/**
     * The User that belong to the seller.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

	/**
	 * A seller may have many sales.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function sales()
	{
		return $this->hasMany(Sale::class);
	}
}
