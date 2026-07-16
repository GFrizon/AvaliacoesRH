<?php

namespace App\Models;

use App\Enums\PerguntaTipo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pergunta extends Model
{
    protected $fillable = [
        'formulario_id',
        'titulo',
        'descricao',
        'tipo',
        'opcoes',
        'obrigatoria',
        'is_active',
        'ordem',
    ];

    protected function casts(): array
    {
        return [
            'tipo' => PerguntaTipo::class,
            'opcoes' => 'array',
            'obrigatoria' => 'boolean',
            'is_active' => 'boolean',
            'ordem' => 'integer',
        ];
    }

    public function formulario(): BelongsTo
    {
        return $this->belongsTo(Formulario::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class);
    }
}
