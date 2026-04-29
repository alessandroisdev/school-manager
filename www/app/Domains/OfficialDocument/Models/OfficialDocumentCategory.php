<?php

namespace App\Domains\OfficialDocument\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Traits\HasUnitScope;

class OfficialDocumentCategory extends Model
{
    use HasFactory, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'name',       // Ex: Ofício, Portaria, Resolução
        'acronym',    // Ex: OF, PORT, RES
    ];
}
