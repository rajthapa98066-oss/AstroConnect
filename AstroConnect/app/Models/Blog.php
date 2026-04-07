<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'astrologer_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'is_published',
        'published_at',
        'review_status',
        'reviewed_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Astrologer author of the blog (null for admin-authored posts).
     */
    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    /**
     * Admin user who last reviewed this blog.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Use slug for implicit route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
