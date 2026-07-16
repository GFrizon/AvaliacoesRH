<?php

namespace App\Enums;

enum PerguntaTipo: string
{
    case TextoCurto = 'texto_curto';
    case TextoLongo = 'texto_longo';
    case Numero = 'numero';
    case Data = 'data';
    case SimNao = 'sim_nao';
    case MultiplaEscolha = 'multipla_escolha';
    case OpcaoUnica = 'opcao_unica';
    case EscalaCinco = 'escala_1_5';
    case EscalaDez = 'escala_1_10';
    case Selecao = 'selecao';
    case Checkbox = 'checkbox';

    public function label(): string
    {
        return match ($this) {
            self::TextoCurto => 'Texto curto',
            self::TextoLongo => 'Texto longo',
            self::Numero => 'Numero',
            self::Data => 'Data',
            self::SimNao => 'Sim/Nao',
            self::MultiplaEscolha => 'Multipla escolha',
            self::OpcaoUnica => 'Opcao unica',
            self::EscalaCinco => 'Escala 1 a 5',
            self::EscalaDez => 'Escala 1 a 10',
            self::Selecao => 'Selecao',
            self::Checkbox => 'Checkbox',
        };
    }
}
