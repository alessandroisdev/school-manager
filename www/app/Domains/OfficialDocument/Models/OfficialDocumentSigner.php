<?php

namespace App\Domains\OfficialDocument\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Infrastructure\Persistence\Traits\HasUnitScope;

class OfficialDocumentSigner extends Model
{
    use HasFactory, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'name',
        'title',
        'signature_image_path',
        'is_active'
    ];
}
