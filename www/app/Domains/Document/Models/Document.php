<?php

namespace App\Domains\Document\Models;

use App\Domains\Enrollment\Models\Student;
use App\Domains\Shared\Models\Unit;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Document extends Model implements AuditableContract
{
    use SoftDeletes, HasUnitScope, Auditable;

    protected $fillable = [
        'unit_id',
        'student_id',
        'document_template_id',
        'title',
        'generated_content',
        'file_path',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function documentTemplate(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class);
    }
}
