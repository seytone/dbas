<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory, SoftDeletes;

	protected $fillable = [
		'attendance_id',
		'date',
		'day',
		'entry',
		'exit',
		'hours',
		'extra',
		'extra_time',
		'extra_apply',
		'missing',
		'missing_time',
		'missing_apply',
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
