<?php

namespace App\Models;

use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Route;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Linked astrologer profile for this user, if one exists.
     */
    public function astrologer(): HasOne
    {
        return $this->hasOne(Astrologer::class);
    }

    /**
     * Appointments booked by this user.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Reviews written by this user.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * True when this user owns an approved astrologer profile.
     */
    public function hasApprovedAstrologerProfile(): bool
    {
        return $this->astrologer?->verification_status === 'approved';
    }

    /**
     * True when account role is a regular user account.
     */
    public function isStandardUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * True when the account is an admin user.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Whether the account is allowed to use user-side booking/review flows.
     */
    public function canAccessUserPanel(): bool
    {
        return $this->isStandardUser() && ! $this->hasApprovedAstrologerProfile();
    }

    /**
     * Whether this user can submit a report for an astrologer.
     */
    public function canReportAstrologer(): bool
    {
        return $this->canAccessUserPanel();
    }

    /**
     * True when the approved astrologer profile is still allowed to access astrologer tools.
     */
    public function canAccessAstrologerPanel(): bool
    {
        return $this->hasApprovedAstrologerProfile()
            && ($this->astrologer?->moderation_status ?? 'active') !== 'disabled';
    }

    /**
     * Resolve post-login redirect path based on role and approval status.
     */
    public function redirectPath(): string
    {
        if ($this->isAdmin() && Route::has('admin.dashboard')) {
            return route('admin.dashboard', absolute: false);
        }

        if ($this->canAccessAstrologerPanel() && Route::has('astrologer.dashboard')) {
            return route('astrologer.dashboard', absolute: false);
        }

        return route('dashboard', absolute: false);
    }
}
