<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantComment extends Model
{
    use HasFactory;

    protected $table = 'plants_comments';

    protected $fillable = [
        'user_id',
        'plant_id',
        'comment',
        'toxicity_level',
        'reported',
        'moderated'
    ];

    /**
     * Relacionamento: um comentário pertence a uma planta.
     */
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    /**
     * Relacionamento: um comentário pertence a um usuário.
     * Mantido igual ao TopicComment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
