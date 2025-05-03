<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'image',
        'status',
    ];

    public function tiles()
    {
        return $this->belongsToMany(Tile::class, 'color_tiles', 'color_id', 'tile_id')
            ->withPivot('priority')
            ->withTimestamps();
    }
}
