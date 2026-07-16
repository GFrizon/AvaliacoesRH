<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table): void {
            $table->timestamp('ultimo_lembrete_em')->nullable()->after('notificado_em');
            $table->unsignedInteger('lembretes_enviados')->default(0)->after('ultimo_lembrete_em');
        });
    }

    public function down(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table): void {
            $table->dropColumn(['ultimo_lembrete_em', 'lembretes_enviados']);
        });
    }
};
