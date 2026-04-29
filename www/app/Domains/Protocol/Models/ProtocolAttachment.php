<?php

namespace App\Domains\Protocol\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProtocolAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocol_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size'
    ];

    public function protocol()
    {
        return $this->belongsTo(DocumentProtocol::class, 'protocol_id');
    }
}
