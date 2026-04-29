<?php

namespace App\Domains\Protocol\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Infrastructure\Persistence\Traits\HasUnitScope;
use Carbon\Carbon;

class DocumentProtocol extends Model
{
    use HasFactory, SoftDeletes, HasUnitScope;

    protected $fillable = [
        'unit_id',
        'protocol_number',
        'sender',
        'subject',
        'received_date',
        'due_date',
        'status',
        'priority',
        'description',
        'received_by_id'
    ];

    protected $casts = [
        'received_date' => 'date',
        'due_date' => 'date',
    ];

    public function receiver()
    {
        return $this->belongsTo(\App\Domains\Auth\Models\User::class, 'received_by_id');
    }

    public function attachments()
    {
        return $this->hasMany(ProtocolAttachment::class, 'protocol_id');
    }

    // Escopos de Alertas
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }

    public function scopeNearDeadline($query)
    {
        return $query->whereNotNull('due_date')
                     ->where('due_date', '<=', Carbon::now()->addDays(3));
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotNull('due_date')
                     ->where('due_date', '<', Carbon::now()->startOfDay());
    }
}
