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
        Schema::table('equipe_obras', function (Blueprint $table) {
            $table->time('hora_saida_almoco')->nullable()->after('hora_entrada');
            $table->time('hora_retorno_almoco')->nullable()->after('hora_saida_almoco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipe_obras', function (Blueprint $table) {
            $table->dropColumn(['hora_saida_almoco', 'hora_retorno_almoco']);
        });
    }
};
