<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * The tiles that belong to the category.
     */
    public function tiles()
    {
        return $this->belongsToMany(Tile::class, 'category_tiles', 'category_id', 'tile_id')
            ->withPivot('priority')
            ->withTimestamps();
    }
}
