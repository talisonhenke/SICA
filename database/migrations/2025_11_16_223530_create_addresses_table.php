<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // FK para usuários (não deixar padrão, porque user_id SEMPRE deve existir)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Dados do endereço
            $table->string('street')->nullable();        // Rua
            $table->string('number')->nullable();        // Número
            $table->string('complement')->nullable();    // Complemento
            $table->string('district')->nullable();      // Bairro
            $table->string('city')->nullable();          // Cidade
            $table->string('state', 2)->nullable();      // Sigla do estado (SP, RJ...)
            $table->string('zip_code', 20)->nullable();  // CEP
            $table->string('country')->default('Brasil');

            // Dados de localização via Google Maps API
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_primary')->default(false); // Endereço principal do usuário

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
