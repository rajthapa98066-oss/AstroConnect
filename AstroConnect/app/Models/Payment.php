<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'astrologer_id',
        'appointment_id',
        'khalti_pidx',
        'amount',
        'status',
        'transaction_id',
        'response_data',
    ];

    protected $casts = [
        'response_data' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * The appointment associated with this payment.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
