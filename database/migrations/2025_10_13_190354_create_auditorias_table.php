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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            $table->string('modelo'); // Nome do modelo (Pessoa, Contrato, etc.)
            $table->unsignedBigInteger('modelo_id'); // ID do registro
            $table->string('acao'); // created, updated, deleted, restored, etc.
            $table->json('dados_anteriores')->nullable(); // Dados antes da mudança
            $table->json('dados_novos')->nullable(); // Dados após a mudança
            $table->unsignedBigInteger('usuario_id')->nullable(); // Quem fez a ação
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            // Índices para performance
            $table->index(['modelo', 'modelo_id']);
            $table->index(['acao']);
            $table->index(['usuario_id']);
            $table->index(['created_at']);

            // Foreign key para usuário
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};