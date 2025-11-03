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
            $table->string('scientific_name', 255);
            $table->string('popular_name', 255);
            $table->string('habitat', 255);
            $table->json('useful_parts');
            $table->text('characteristics');
            $table->text('observations');
            $table->text('popular_use');
            $table->text('chemical_composition');
            $table->text('contraindications');
            $table->text('mode_of_use');
            $table->json('images');
            $table->text('info_references');
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
