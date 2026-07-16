<?php

namespace App\Support;

use App\Models\UnidadeNegocio;
use Illuminate\Support\Collection;

class UnidadesNegocio
{
    public static function options(?int $empresaId = null): Collection
    {
        if (! $empresaId) {
            return collect();
        }

        return UnidadeNegocio::where('empresa_id', $empresaId)
            ->where('is_active', true)
            ->orderBy('nome')
            ->pluck('nome');
    }
}
