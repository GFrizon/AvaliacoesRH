<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('colaboradores', function (Blueprint $table): void {
            $table->foreignId('gestor_id')->nullable()->after('setor_id')->constrained('users')->nullOnDelete();
            $table->foreignId('formulario_id')->nullable()->after('gestor_id')->constrained('formularios')->nullOnDelete();
        });

        Schema::table('avaliacoes', function (Blueprint $table): void {
            $table->timestamp('notificado_em')->nullable()->after('concluida_em');
            $table->timestamp('cancelada_em')->nullable()->after('notificado_em');
            $table->text('motivo_cancelamento')->nullable()->after('cancelada_em');
        });
    }

    public function down(): void
    {
        Schema::table('avaliacoes', function (Blueprint $table): void {
            $table->dropColumn(['notificado_em', 'cancelada_em', 'motivo_cancelamento']);
        });

        Schema::table('colaboradores', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('formulario_id');
            $table->dropConstrainedForeignId('gestor_id');
        });
    }
};
