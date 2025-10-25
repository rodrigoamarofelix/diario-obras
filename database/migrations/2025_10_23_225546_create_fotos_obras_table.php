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
        Schema::create('fotos_obras', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->foreignId('atividade_id')->nullable()->constrained('atividade_obras')->onDelete('set null');
            $table->foreignId('equipe_id')->nullable()->constrained('equipe_obras')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Dados da foto
            $table->string('titulo')->nullable();
            $table->text('descricao')->nullable();
            $table->string('caminho_arquivo');
            $table->string('nome_arquivo');
            $table->string('mime_type');
            $table->bigInteger('tamanho_arquivo'); // em bytes
            $table->string('hash_arquivo')->unique(); // para evitar duplicatas

            // Geolocalização
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('altitude', 8, 2)->nullable(); // em metros
            $table->decimal('precisao', 8, 2)->nullable(); // em metros

            // Metadados EXIF
            $table->string('camera_marca')->nullable();
            $table->string('camera_modelo')->nullable();
            $table->string('lente')->nullable();
            $table->decimal('aperture', 4, 2)->nullable(); // f/2.8
            $table->decimal('shutter_speed', 8, 4)->nullable(); // 1/60
            $table->integer('iso')->nullable();
            $table->decimal('focal_length', 6, 2)->nullable(); // em mm

            // Sistema de tags
            $table->json('tags')->nullable(); // ['antes', 'progresso', 'problema']
            $table->string('categoria')->default('geral'); // antes, progresso, problema, solucao, final

            // Status e controle
            $table->boolean('publica')->default(true);
            $table->boolean('aprovada')->default(false);
            $table->timestamp('data_captura')->nullable();
            $table->timestamp('data_upload')->useCurrent();

            // Soft deletes
            $table->softDeletes();

            $table->timestamps();

            // Índices
            $table->index(['projeto_id', 'data_captura']);
            $table->index(['categoria', 'aprovada']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fotos_obras');
    }
};
