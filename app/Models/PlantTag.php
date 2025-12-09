<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantTag extends Model
{
    protected $table = 'plant_tag';

    protected $fillable = [
        'plant_id',
        'tag_id',
    ];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }
}
