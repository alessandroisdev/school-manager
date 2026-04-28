<?php

namespace App\Domains\Enrollment\Enums;

enum EnrollmentStatus: string
{
    case Ativa = 'ativa';
    case Concluida = 'concluída';
    case Trancada = 'trancada';
    case Transferida = 'transferida';
}
