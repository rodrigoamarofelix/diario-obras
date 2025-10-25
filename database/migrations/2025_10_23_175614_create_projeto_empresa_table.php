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
        Schema::create('projeto_empresa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->enum('tipo_participacao', [
                'construtora',
                'fornecedor',
                'subcontratada',
                'consultoria',
                'fiscalizacao',
                'outros'
            ])->default('outros');
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();

            // Evitar duplicatas
            $table->unique(['projeto_id', 'empresa_id', 'tipo_participacao']);

            // Índices para performance
            $table->index(['projeto_id']);
            $table->index(['empresa_id']);
            $table->index(['tipo_participacao']);
            $table->index(['ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projeto_empresa');
    }
};
