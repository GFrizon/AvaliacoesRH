<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    protected $fillable = [
        'empresa_id',
        'avaliacao_id',
        'tipo',
        'destinatario',
        'assunto',
        'status',
        'enfileirado_em',
        'erro',
    ];

    protected function casts(): array
    {
        return [
            'enfileirado_em' => 'datetime',
        ];
    }

    public function avaliacao(): BelongsTo
    {
        return $this->belongsTo(Avaliacao::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
