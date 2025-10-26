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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('razao_social');
            $table->string('cnpj', 18)->unique();
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('cep', 10);
            $table->string('endereco');
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro');
            $table->string('cidade');
            $table->string('estado', 2);
            $table->string('pais', 50)->default('Brasil');
            $table->string('site')->nullable();
            $table->text('observacoes')->nullable();
            $table->boolean('ativo')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['cnpj']);
            $table->index(['nome']);
            $table->index(['razao_social']);
            $table->index(['ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};




