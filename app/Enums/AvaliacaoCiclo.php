<?php

namespace App\Enums;

enum AvaliacaoCiclo: string
{
    case NoventaDias = '90_dias';
    case SeisMeses = '6_meses';
    case UmAno = '1_ano';

    public function label(): string
    {
        return match ($this) {
            self::NoventaDias => '90 dias',
            self::SeisMeses => '6 meses',
            self::UmAno => '1 ano',
        };
    }
}
