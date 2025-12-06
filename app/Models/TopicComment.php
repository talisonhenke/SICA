<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicComment extends Model
{
    use HasFactory;

    protected $table = 'topic_comments';

    protected $fillable = [
        'topic_id',
        'user_id',
        'comment',
        'toxicity_level'
    ];

    /**
     * Relacionamento: um comentário pertence a um tópico.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * Relacionamento: um comentário pertence a um usuário.
     * Remova se você não estiver usando autenticação.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
