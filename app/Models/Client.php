<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, SoftDeletes;

	protected $fillable = ['code', 'title', 'document', 'email', 'phone', 'address'];

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
		return $this->code . ' - ' . $this->title;
	}
}
