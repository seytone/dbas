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
		'missing',
		'manual_fix',
		'total',
		'payment',
		'payment_date',
		'payment_evidence',
		'comments',
	];

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $guard_name = 'web';

	/**
	 * An attendance belongs to an employee.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	/**
	 * An attendance has many records.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function records()
	{
		return $this->hasMany(AttendanceRecord::class);
	}
}
