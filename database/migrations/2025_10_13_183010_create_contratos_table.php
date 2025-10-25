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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->text('descricao');
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->unsignedBigInteger('gestor_id');
            $table->unsignedBigInteger('fiscal_id');
            $table->enum('status', ['ativo', 'inativo', 'vencido', 'suspenso'])->default('ativo');
            $table->timestamps();
            $table->softDeletes(); // Soft delete para histórico

            // Foreign keys para pessoas (gestor e fiscal)
            $table->foreign('gestor_id')->references('id')->on('pessoas')->onDelete('cascade');
            $table->foreign('fiscal_id')->references('id')->on('pessoas')->onDelete('cascade');

            // Índices
            $table->index('numero');
            $table->index('data_inicio');
            $table->index('data_fim');
            $table->index('status');
            $table->index('gestor_id');
            $table->index('fiscal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
