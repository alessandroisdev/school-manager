<?php

namespace App\Domains\Auth\Enums;

enum DefaultRoles: string
{
    case Admin = 'admin';
    case Diretor = 'diretor';
    case Secretaria = 'secretaria';
    case Professor = 'professor';
    case Aluno = 'aluno';
    case Responsavel = 'responsavel';
}
