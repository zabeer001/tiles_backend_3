<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tile extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'grid_category',
        'description',
        'image',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_tiles', 'tile_id', 'category_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'color_tiles', 'tile_id', 'color_id')
                    ->withPivot('priority')
                    ->withTimestamps();
    }
}
