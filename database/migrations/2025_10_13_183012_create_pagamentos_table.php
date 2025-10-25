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
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medicao_id');
            $table->string('numero_pagamento');
            $table->date('data_pagamento');
            $table->decimal('valor_pagamento', 10, 2);
            $table->text('observacoes')->nullable();
            $table->string('documento_redmine')->nullable(); // Link ou referência do documento do Redmine
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado', 'pago'])->default('pendente');
            $table->unsignedBigInteger('usuario_id');
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('medicao_id')->references('id')->on('medicoes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');

            // Índices
            $table->index('numero_pagamento');
            $table->index('data_pagamento');
            $table->index('status');
            $table->index('medicao_id');
            $table->index('usuario_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};


