<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    
    protected $fillable = [
        'name',
        'description',
    ];

    public function plants()
    {
        return $this->belongsToMany(Plant::class, 'plant_tag', 'tag_id', 'plant_id')
                    ->withTimestamps();
    }
}
