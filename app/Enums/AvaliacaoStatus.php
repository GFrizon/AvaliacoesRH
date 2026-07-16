<?php

namespace App\Enums;

enum AvaliacaoStatus: string
{
    case Agendada = 'agendada';
    case Pendente = 'pendente';
    case Concluida = 'concluida';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Agendada => 'Agendada',
            self::Pendente => 'Pendente',
            self::Concluida => 'Concluida',
            self::Cancelada => 'Cancelada',
        };
    }
}
