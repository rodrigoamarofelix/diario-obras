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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('endereco');
            $table->string('cidade');
            $table->string('estado');
            $table->string('cep')->nullable();
            $table->string('cliente');
            $table->string('contrato')->nullable();
            $table->decimal('valor_total', 15, 2)->nullable();
            $table->date('data_inicio');
            $table->date('data_fim_prevista')->nullable();
            $table->date('data_fim_real')->nullable();
            $table->enum('status', ['planejamento', 'em_andamento', 'pausado', 'concluido', 'cancelado'])->default('planejamento');
            $table->enum('prioridade', ['baixa', 'media', 'alta', 'urgente'])->default('media');
            $table->text('observacoes')->nullable();
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
        Schema::dropIfExists('projetos');
    }
};
