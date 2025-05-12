<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'message',
        'tile_name',
        'quantity_unit',
        'quantity_needed',
        'status',
        'referred_by',
        'other_specify',
        'grout_color',
        'grout_thickness',
        'grid_category',
        'rotations',
        'svg_base64',
    ];

    protected $casts = [
        'rotations' => 'array',
    ];
}
