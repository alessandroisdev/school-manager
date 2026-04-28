<?php

namespace App\Domains\Evaluation\Models;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeEntry extends Model
{
    use SoftDeletes;

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
