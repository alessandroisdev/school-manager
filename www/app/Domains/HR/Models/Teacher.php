<?php

namespace App\Domains\HR\Models;

use App\Domains\Academic\Models\TeacherAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;

class Teacher extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'employee_id',
        'specialty',
        'max_workload',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(\App\Domains\Academic\Models\Subject::class);
    }
}
