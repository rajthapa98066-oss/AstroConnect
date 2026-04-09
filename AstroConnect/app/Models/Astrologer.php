<?php

namespace App\Models;

use App\Models\Review;
use App\Models\AstrologerReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Astrologer extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'specialization',
        'experience_years',
        'bio',
        'consultation_fee',
        'availability_status',
        'profile_photo',
        'verification_status',
        'moderation_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'consultation_fee' => 'decimal:2',
        ];
    }

    /**
     * User account that owns this astrologer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Appointments assigned to this astrologer.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Blogs authored by this astrologer.
     */
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    /**
     * Reviews left by users for this astrologer.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Reports filed against this astrologer.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(AstrologerReport::class);
    }

    /**
     * Whether this account has been disabled by admin moderation.
     */
    public function isDisabled(): bool
    {
        return ($this->moderation_status ?? 'active') === 'disabled';
    }
}
