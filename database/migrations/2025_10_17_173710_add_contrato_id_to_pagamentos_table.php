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
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->unsignedBigInteger('contrato_id')->nullable()->after('medicao_id');
            $table->foreign('contrato_id')->references('id')->on('contratos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropForeign(['contrato_id']);
            $table->dropColumn('contrato_id');
        });
    }
};
