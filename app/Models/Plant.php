<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    protected $table = 'plants';

    protected $fillable = [
        'scientific_name',
        'popular_name',
        'slug',
        'habitat',
        'characteristics',
        'observations',
        'popular_use',
        'chemical_composition',
        'contraindications',
        'mode_of_use',
        'images',
        'info_references',
        'qr_code',
    ];

    protected $casts = [
        'useful_parts' => 'array',
        //'images' => 'array',
    ];
}
