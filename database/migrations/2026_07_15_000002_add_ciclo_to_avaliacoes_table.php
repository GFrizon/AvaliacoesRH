<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table): void {
            $table->string('ciclo')->default('90_dias')->after('formulario_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table): void {
            $table->dropColumn('ciclo');
        });
    }
};
