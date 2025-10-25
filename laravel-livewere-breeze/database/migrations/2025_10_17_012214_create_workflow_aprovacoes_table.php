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
        Schema::create('workflow_aprovacoes', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // App\Models\Medicao, App\Models\Pagamento, etc.
            $table->unsignedBigInteger('model_id'); // ID do modelo relacionado
            $table->string('tipo'); // 'medicao', 'pagamento', 'contrato', 'usuario'
            $table->enum('status', ['pendente', 'em_analise', 'aprovado', 'rejeitado', 'suspenso'])->default('pendente');
            $table->unsignedBigInteger('solicitante_id'); // Quem solicitou
            $table->unsignedBigInteger('aprovador_id')->nullable(); // Quem deve aprovar
            $table->unsignedBigInteger('aprovado_por')->nullable(); // Quem aprovou
            $table->timestamp('aprovado_em')->nullable(); // Quando foi aprovado
            $table->text('comentarios')->nullable(); // Comentários do aprovador
            $table->text('justificativa_rejeicao')->nullable(); // Justificativa se rejeitado
            $table->integer('nivel_aprovacao')->default(1); // Nível atual de aprovação
            $table->integer('nivel_maximo')->default(1); // Máximo de níveis necessários
            $table->decimal('valor', 15, 2)->nullable(); // Valor para regras de aprovação
            $table->json('dados_extras')->nullable(); // Dados adicionais específicos
            $table->timestamp('prazo_aprovacao')->nullable(); // Prazo para aprovação
            $table->boolean('urgente')->default(false); // Se é urgente
            $table->timestamps();

            // Índices
            $table->index(['model_type', 'model_id']);
            $table->index(['tipo', 'status']);
            $table->index(['aprovador_id', 'status']);
            $table->index(['solicitante_id']);
            $table->index(['prazo_aprovacao']);
            $table->index(['urgente', 'status']);

            // Foreign keys
            $table->foreign('solicitante_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('aprovador_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('aprovado_por')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_aprovacoes');
    }
};