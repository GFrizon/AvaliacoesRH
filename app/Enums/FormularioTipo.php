<?php

namespace App\Enums;

enum FormularioTipo: string
{
    case Administrativo = 'administrativo';
    case ComercialEngenharia = 'comercial_engenharia';
    case Industria = 'industria';

    public function label(): string
    {
        return match ($this) {
            self::Administrativo => 'Administrativo',
            self::ComercialEngenharia => 'Comercial e Engenharia',
            self::Industria => 'Industria',
        };
    }
}
