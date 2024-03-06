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
            $table->text('useful_parts');
            $table->text('characteristics');
            $table->text('article');
            $table->text('observations');
            $table->text('chemical_composition');
            $table->text('contraindications');
            $table->text('mode_of_use');
            $table->text('pharmacological_actions');
            $table->text('images');
            $table->text('references');
            $table->string('tags', 255)->nullable();
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
        Schema::dropIfExists('plants');
    }
};
