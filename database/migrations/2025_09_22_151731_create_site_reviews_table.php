<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('site_reviews', function (Blueprint $table) {
            $table->id(); // ID da avaliação
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID do usuário que fez a avaliação
            $table->tinyInteger('rating'); // Nota de 1 a 5
            $table->text('comment')->nullable(); // Comentário opcional
            $table->timestamps(); // created_at e updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('site_reviews');
    }
};
