<?php

namespace App\Domains\Document\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUnitScope;

class IssuedDocument extends Model
{
    use HasFactory, SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'student_id',
        'document_template_id',
        'reference_code',
        'content',
        'status',
        'rectified_by_id',
        'issued_by_id',
    ];

    public function student()
    {
        return $this->belongsTo(\App\Domains\Enrollment\Models\Student::class);
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    public function issuer()
    {
        return $this->belongsTo(\App\Domains\Shared\Models\User::class, 'issued_by_id');
    }

    public function rectifiedBy()
    {
        return $this->belongsTo(self::class, 'rectified_by_id');
    }
}
