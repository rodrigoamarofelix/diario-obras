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
        Schema::create('notificacaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('tipo'); // info, success, warning, error
            $table->string('titulo');
            $table->text('mensagem');
            $table->string('icone')->nullable(); // FontAwesome icon class
            $table->string('cor')->default('info'); // Bootstrap color class
            $table->json('dados')->nullable(); // Dados extras (URLs, IDs, etc)
            $table->boolean('lida')->default(false);
            $table->timestamp('lida_em')->nullable();
            $table->string('acao')->nullable(); // Ação que gerou a notificação
            $table->string('modelo')->nullable(); // Modelo relacionado
            $table->unsignedBigInteger('modelo_id')->nullable(); // ID do modelo relacionado
            $table->timestamps();

            $table->index(['user_id', 'lida']);
            $table->index(['tipo', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacaos');
    }
};
