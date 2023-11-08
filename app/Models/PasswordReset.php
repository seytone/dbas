<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PasswordReset extends Model
{
    use HasFactory;

    /**
     * The attributes that are guarded.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
