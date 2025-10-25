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
        Schema::create('contrato_anexos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_id');
            $table->string('nome_original'); // Nome original do arquivo
            $table->string('nome_arquivo'); // Nome do arquivo no servidor
            $table->string('caminho'); // Caminho completo do arquivo
            $table->string('tipo_mime'); // Tipo MIME do arquivo
            $table->unsignedBigInteger('tamanho'); // Tamanho em bytes
            $table->text('descricao')->nullable(); // Descrição opcional do anexo
            $table->unsignedBigInteger('usuario_id'); // Quem fez o upload
            $table->timestamps();

            // Foreign keys
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            // Índices
            $table->index('contrato_id');
            $table->index('usuario_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_anexos');
    }
};