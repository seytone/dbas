<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleService extends Model
{
    use HasFactory;

	protected $fillable = ['sale_id', 'service_id', 'quantity', 'price', 'discount', 'total'];
}
