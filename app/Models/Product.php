<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos via create() ou update()
    protected $fillable = [
        'plant_id',
        'name',
        'description',
        'price',
        'stock',
        'status',
        'image',
    ];

    // Relação: um produto pertence a uma planta
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
