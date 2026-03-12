<?php

namespace App\Models;

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

    public function astrologer(): HasOne
    {
        return $this->hasOne(Astrologer::class);
    }

    public function redirectPath(): string
    {
        if ($this->role === 'admin' && Route::has('admin.dashboard')) {
            return route('admin.dashboard', absolute: false);
        }

        if ($this->astrologer && $this->astrologer->verification_status === 'approved' && Route::has('astrologer.dashboard')) {
            return route('astrologer.dashboard', absolute: false);
        }

        return route('dashboard', absolute: false);
    }
}
