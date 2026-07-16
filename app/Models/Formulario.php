<?php

namespace App\Models;

use App\Enums\FormularioTipo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Formulario extends Model
{
    protected $fillable = ['empresa_id', 'nome', 'descricao', 'tipo', 'versao', 'is_active'];

    protected function casts(): array
    {
        return [
            'tipo' => FormularioTipo::class,
            'is_active' => 'boolean',
            'versao' => 'integer',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function perguntas(): HasMany
    {
        return $this->hasMany(Pergunta::class)->orderBy('ordem');
    }
}
