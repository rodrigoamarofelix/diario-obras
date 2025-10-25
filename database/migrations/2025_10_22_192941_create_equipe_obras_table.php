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
        Schema::create('equipe_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->foreignId('atividade_id')->nullable()->constrained('atividade_obras');
            $table->foreignId('funcionario_id')->constrained('users');
            $table->date('data_trabalho');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_saida')->nullable();
            $table->integer('horas_trabalhadas')->nullable();
            $table->enum('funcao', ['pedreiro', 'eletricista', 'encanador', 'pintor', 'carpinteiro', 'ajudante', 'engenheiro', 'arquiteto', 'supervisor', 'outros'])->default('ajudante');
            $table->text('atividades_realizadas')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('presente')->default(true);
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
        Schema::dropIfExists('equipe_obras');
    }
};
