<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_image',
        'name',
        'email',
        'phone_number',
        'quantity',
        'quantity_per_unit',
        'status',
        'refer_by',
        'notes',
        'message',
        'image_svg_text',
    ];
}
