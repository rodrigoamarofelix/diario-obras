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
        Schema::create('contrato_responsaveis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contrato_id');
            $table->unsignedBigInteger('gestor_id');
            $table->unsignedBigInteger('fiscal_id');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable(); // NULL = ainda ativo
            $table->timestamps();

            // Foreign keys
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
            $table->foreign('gestor_id')->references('id')->on('pessoas')->onDelete('cascade');
            $table->foreign('fiscal_id')->references('id')->on('pessoas')->onDelete('cascade');

            // Índices
            $table->index('contrato_id');
            $table->index('gestor_id');
            $table->index('fiscal_id');
            $table->index('data_inicio');
            $table->index('data_fim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrato_responsaveis');
    }
};
