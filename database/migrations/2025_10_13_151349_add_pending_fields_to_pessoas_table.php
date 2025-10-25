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
        Schema::table('pessoas', function (Blueprint $table) {
            $table->enum('status_validacao', ['pendente', 'validado', 'rejeitado'])->default('pendente')->after('status');
            $table->text('observacoes_validacao')->nullable()->after('status_validacao');
            $table->timestamp('data_validacao')->nullable()->after('observacoes_validacao');
            $table->timestamp('data_ultima_tentativa')->nullable()->after('data_validacao');
            $table->integer('tentativas_validacao')->default(0)->after('data_ultima_tentativa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropColumn([
                'status_validacao',
                'observacoes_validacao',
                'data_validacao',
                'data_ultima_tentativa',
                'tentativas_validacao'
            ]);
        });
    }
};
