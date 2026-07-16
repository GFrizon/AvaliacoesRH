<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = ['empresa_id', 'chave', 'valor'];

    protected function casts(): array
    {
        return ['valor' => 'array'];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}
