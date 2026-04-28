<?php

namespace App\Domains\Enrollment\Enums;

enum PreEnrollmentStatus: string
{
    case Pendente = 'pendente';
    case Aprovada = 'aprovada';
    case Rejeitada = 'rejeitada';
}
