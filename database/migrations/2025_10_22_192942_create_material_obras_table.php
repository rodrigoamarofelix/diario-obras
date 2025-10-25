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
        Schema::create('material_obras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained('projetos');
            $table->foreignId('atividade_id')->nullable()->constrained('atividade_obras');
            $table->string('nome_material');
            $table->text('descricao')->nullable();
            $table->string('unidade_medida'); // kg, m², m³, unidades, etc.
            $table->decimal('quantidade', 10, 3);
            $table->decimal('valor_unitario', 10, 2)->nullable();
            $table->decimal('valor_total', 15, 2)->nullable();
            $table->enum('tipo_movimento', ['entrada', 'saida', 'transferencia'])->default('entrada');
            $table->date('data_movimento');
            $table->string('fornecedor')->nullable();
            $table->string('nota_fiscal')->nullable();
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
        Schema::dropIfExists('material_obras');
    }
};
