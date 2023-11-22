<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, SoftDeletes, CascadeSoftDeletes;

	protected $cascadeDeletes = ['invoices'];

	protected $fillable = [
		'code',
		'title',
		'document',
		'email',
		'phone',
		'address'
	];

	/**
     * A client has many invoices.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function invoices()
    {
        return $this->hasMany(Sale::class);
    }

	public function getIdentification()
	{
		return $this->code . ' :: ' . $this->title;
	}
}
