<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'astrologer_id',
        'scheduled_at',
        'duration_minutes',
        'topic',
        'message',
        'status',
        'rating',
        'rated_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'duration_minutes' => 'integer',
            'rating' => 'integer',
            'rated_at' => 'datetime',
        ];
    }

    /**
     * User who created the appointment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Astrologer selected for the appointment.
     */
    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }
}
