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
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('colaborador_id')->constrained('colaboradores');
            $table->foreignId('gestor_id')->constrained('users');
            $table->foreignId('formulario_id')->constrained('formularios');
            $table->foreignId('criada_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pendente')->index();
            $table->date('data_limite');
            $table->timestamp('iniciada_em')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->boolean('efetivar')->nullable();
            $table->text('observacoes_finais')->nullable();
            $table->json('snapshot_formulario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
