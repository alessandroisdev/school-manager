<?php

namespace App\Domains\Evaluation\Enums;

enum PerformanceStatus: string
{
    case Aprovado = 'aprovado';
    case Reprovado = 'reprovado';
    case Recuperacao = 'recuperacao';
}
