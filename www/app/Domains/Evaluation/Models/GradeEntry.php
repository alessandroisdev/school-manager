<?php

namespace App\Domains\Evaluation\Models;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class GradeEntry extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'evaluation_id',
        'student_id',
        'score',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'float',
        ];
    }

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
