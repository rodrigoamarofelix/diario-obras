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
        Schema::create('foto_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->foreignId('atividade_id')->nullable()->constrained('atividade_obras');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('caminho_arquivo');
            $table->string('nome_arquivo');
            $table->string('tipo_arquivo'); // jpg, png, pdf, etc.
            $table->integer('tamanho_arquivo')->nullable(); // em bytes
            $table->enum('categoria', ['antes', 'durante', 'depois', 'problema', 'solucao', 'documento', 'outros'])->default('durante');
            $table->date('data_foto');
            $table->time('hora_foto')->nullable();
            $table->text('localizacao')->nullable(); // onde a foto foi tirada
            $table->text('observacoes')->nullable();
            $table->foreignId('fotografo_id')->constrained('users');
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
        Schema::dropIfExists('foto_obras');
    }
};
