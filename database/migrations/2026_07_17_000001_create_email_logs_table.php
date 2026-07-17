<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('avaliacao_id')->nullable()->constrained('avaliacoes')->nullOnDelete();
            $table->string('tipo');
            $table->string('destinatario');
            $table->string('assunto')->nullable();
            $table->string('status')->default('enfileirado');
            $table->timestamp('enfileirado_em')->nullable();
            $table->text('erro')->nullable();
            $table->timestamps();

            $table->index(['empresa_id', 'created_at']);
            $table->index(['avaliacao_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
