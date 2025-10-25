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
        Schema::create('pessoas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->unsignedBigInteger('lotacao_id');
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();

            // Foreign key para lotações
            $table->foreign('lotacao_id')->references('id')->on('lotacoes')->onDelete('cascade');

            // Índices
            $table->index('cpf');
            $table->index('lotacao_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pessoas');
    }
};
