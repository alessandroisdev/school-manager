<?php

namespace App\Domains\OfficialDocument\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Persistence\Traits\HasUnitScope;

class OfficialDocument extends Model
{
    use HasFactory, SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'category_id',
        'year',
        'number',
        'full_number',
        'date',
        'recipient',
        'subject',
        'content',
        'status',
        'signer_name',
        'signer_title',
        'signature_image_path',
        'created_by_id'
    ];

    public function category()
    {
        return $this->belongsTo(OfficialDocumentCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(\App\Domains\Shared\Models\User::class, 'created_by_id');
    }
}
