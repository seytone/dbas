<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

	protected $fillable = [
		'number',
		'pin',
		'name',
		'lastname',
		'email',
		'phone',
		'salary',
		'position',
		'department',
		'active',
	];

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $guard_name = 'web';

	/**
	 * An employee has many attendances.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function attendances()
	{
		return $this->hasMany(Attendance::class);
	}
}
