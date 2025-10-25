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
        Schema::create('medicoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalogo_id');
            $table->unsignedBigInteger('contrato_id');
            $table->unsignedBigInteger('lotacao_id');
            $table->string('numero_medicao');
            $table->date('data_medicao');
            $table->decimal('quantidade', 10, 3);
            $table->decimal('valor_unitario', 10, 2);
            $table->decimal('valor_total', 10, 2);
            $table->text('observacoes')->nullable();
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('catalogo_id')->references('id')->on('catalogos')->onDelete('cascade');
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
            $table->foreign('lotacao_id')->references('id')->on('lotacoes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            // Índices
            $table->index('numero_medicao');
            $table->index('data_medicao');
            $table->index('status');
            $table->index('catalogo_id');
            $table->index('contrato_id');
            $table->index('lotacao_id');
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicoes');
    }
};


