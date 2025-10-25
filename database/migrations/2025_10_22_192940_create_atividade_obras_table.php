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
        Schema::create('atividade_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->date('data_atividade');
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('tipo', ['construcao', 'demolicao', 'reforma', 'manutencao', 'limpeza', 'outros'])->default('construcao');
            $table->enum('status', ['planejado', 'em_andamento', 'concluido', 'cancelado'])->default('planejado');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fim')->nullable();
            $table->integer('tempo_gasto_minutos')->nullable();
            $table->text('observacoes')->nullable();
            $table->text('problemas_encontrados')->nullable();
            $table->text('solucoes_aplicadas')->nullable();
            $table->foreignId('responsavel_id')->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atividade_obras');
    }
};
