<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

	protected $fillable = [
		'employee_id',
		'year',
		'month',
		'hours',
		'extra',
		'payment',
		'comments',
	];

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $guard_name = 'web';
}
