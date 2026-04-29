<?php

namespace App\Domains\Finance\Models;

use App\Domains\Enrollment\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceCampaignItem extends Model
{
    protected $fillable = [
        'invoice_campaign_id',
        'student_id',
        'invoice_id',
        'email',
        'status', // pending, sent, failed
        'error_message',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(InvoiceCampaign::class, 'invoice_campaign_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
