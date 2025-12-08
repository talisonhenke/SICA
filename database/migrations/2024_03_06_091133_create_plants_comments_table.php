<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plants_comments', function (Blueprint $table) {
            $table->id('id');

            // Usuário autor do comentário
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Texto do comentário
            $table->string('comment');

            // Planta relacionada
            $table->unsignedBigInteger('plant_id')->nullable();
            $table->foreign('plant_id')->references('id')->on('plants')->onDelete('cascade');

            // Novos campos adicionados
            $table->float('toxicity_level', 5, 2)->nullable()->default(0.0);

            // 0 = não reportado / 1 = reportado
            $table->tinyInteger('reported')->default(0);

            // 0 = não moderado / 1 = moderado (ocultado pelo admin)
            $table->tinyInteger('moderated')->default(0);

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plants_comments');
    }
};
