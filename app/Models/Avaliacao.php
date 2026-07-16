<?php

namespace App\Models;

use App\Enums\AvaliacaoCiclo;
use App\Enums\AvaliacaoStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Avaliacao extends Model
{
    protected $table = 'avaliacoes';

    protected $fillable = [
        'empresa_id',
        'colaborador_id',
        'gestor_id',
        'formulario_id',
        'ciclo',
        'criada_por',
        'status',
        'data_limite',
        'iniciada_em',
        'concluida_em',
        'notificado_em',
        'ultimo_lembrete_em',
        'lembretes_enviados',
        'cancelada_em',
        'motivo_cancelamento',
        'efetivar',
        'observacoes_finais',
        'snapshot_formulario',
    ];

    protected function casts(): array
    {
        return [
            'status' => AvaliacaoStatus::class,
            'ciclo' => AvaliacaoCiclo::class,
            'data_limite' => 'date',
            'iniciada_em' => 'datetime',
            'concluida_em' => 'datetime',
            'notificado_em' => 'datetime',
            'ultimo_lembrete_em' => 'datetime',
            'lembretes_enviados' => 'integer',
            'cancelada_em' => 'datetime',
            'efetivar' => 'boolean',
            'snapshot_formulario' => 'array',
        ];
    }

    public function colaborador(): BelongsTo
    {
        return $this->belongsTo(Colaborador::class);
    }

    public function gestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gestor_id');
    }

    public function formulario(): BelongsTo
    {
        return $this->belongsTo(Formulario::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(Resposta::class);
    }

    public function getDiasRestantesAttribute(): int
    {
        return now()->startOfDay()->diffInDays($this->data_limite, false);
    }
}
