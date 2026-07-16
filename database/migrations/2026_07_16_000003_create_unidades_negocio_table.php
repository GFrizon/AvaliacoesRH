<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_negocio', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->string('nome');
            $table->string('descricao')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['empresa_id', 'nome']);
        });

        $empresas = DB::table('empresas')->pluck('id');

        foreach ($empresas as $empresaId) {
            $unidades = collect(['Bakof Tec', 'Bakof Matriz'])
                ->merge(
                    DB::table('colaboradores')
                        ->where('empresa_id', $empresaId)
                        ->whereNotNull('unidade_negocio')
                        ->where('unidade_negocio', '!=', '')
                        ->distinct()
                        ->pluck('unidade_negocio')
                )
                ->map(fn (string $unidade) => trim($unidade))
                ->filter()
                ->unique();

            foreach ($unidades as $unidade) {
                DB::table('unidades_negocio')->updateOrInsert(
                    ['empresa_id' => $empresaId, 'nome' => $unidade],
                    ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_negocio');
    }
};
