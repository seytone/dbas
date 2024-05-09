<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'config';

	protected $fillable = [
		'key',
		'value',
		'description',
		'status'
	];
}
