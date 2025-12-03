<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status')->nullable(); // ex: "pending", "preparing", "shipped", "delivered", "canceled"
            $table->text('notes')->nullable(); // observações ou comentários
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_history');
    }
};