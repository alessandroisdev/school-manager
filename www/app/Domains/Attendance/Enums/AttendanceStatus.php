<?php

namespace App\Domains\Attendance\Enums;

enum AttendanceStatus: string
{
    case Presente = 'presente';
    case Falta = 'falta';
    case Justificado = 'justificado';
}
