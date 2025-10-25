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
            $table->foreignId('funcao_id')->nullable()->constrained('funcoes')->onDelete('set null');
            $table->index(['funcao_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pessoas', function (Blueprint $table) {
            $table->dropForeign(['funcao_id']);
            $table->dropIndex(['funcao_id']);
            $table->dropColumn('funcao_id');
        });
    }
};
