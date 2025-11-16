<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plants', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name', 255)->nullable();
            $table->string('popular_name', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('habitat', 255)->nullable();
            $table->json('useful_parts')->nullable();
            $table->text('characteristics')->nullable();
            $table->text('observations')->nullable();
            $table->text('popular_use')->nullable();
            $table->text('chemical_composition')->nullable();
            $table->text('contraindications')->nullable();
            $table->text('mode_of_use')->nullable();
            $table->json('images')->nullable();
            $table->text('info_references')->nullable();
            $table->text('qr_code')->nullable(); // pode ser link/identificador
            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plants');
    }
};
