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
            $table->enum('tipo_almoco', ['integral', 'reduzido'])->default('integral')->after('hora_entrada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipe_obras', function (Blueprint $table) {
            $table->dropColumn('tipo_almoco');
        });
    }
};
