<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\User;
use App\Notifications\BlogSubmittedForReviewAdminNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AstrologerBlogController extends Controller
{
    /**
     * Show blogs created by the logged-in astrologer.
     */
    public function index(Request $request): View
    {
        $astrologer = $request->user()->astrologer;

        $blogs = Blog::query()
            ->where('astrologer_id', $astrologer->id)
            ->latest()
            ->paginate(12);

        return view('pages.astrologer.blog', [
            'mode' => 'list',
            'blogs' => $blogs,
        ]);
    }

    /**
     * Render the astrologer blog creation form.
     */
    public function create(): View
    {
        return view('pages.astrologer.blog', [
            'mode' => 'create',
        ]);
    }

    /**
     * Save a new astrologer blog as pending and hidden until admin review.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBlog($request);
        $validated['slug'] = $this->resolveUniqueSlug($validated['slug'] ?? null, $validated['title']);

        $blog = Blog::create([
            'astrologer_id' => $request->user()->astrologer->id,
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'category' => $validated['category'] ?? null,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'is_published' => false,
            'published_at' => null,
            'review_status' => 'pending',
            'reviewed_by' => null,
        ])->loadMissing('astrologer.user');

        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(function (User $admin) use ($blog): void {
                $admin->notify(new BlogSubmittedForReviewAdminNotification($blog));
            });

        return redirect()
            ->route('astrologer.blogs.index')
            ->with('status', 'blog-submitted');
    }

    /**
     * Render edit form for astrologer's own blog only.
     */
    public function edit(Request $request, Blog $blog): View
    {
        $astrologer = $request->user()->astrologer;
        abort_unless($blog->astrologer_id === $astrologer->id, 403);

        return view('pages.astrologer.blog', [
            'mode' => 'edit',
            'blog' => $blog,
        ]);
    }

    /**
     * Update astrologer blog and reset it to pending review.
     */
    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $astrologer = $request->user()->astrologer;
        abort_unless($blog->astrologer_id === $astrologer->id, 403);

        $validated = $this->validateBlog($request, $blog);
        $validated['slug'] = $this->resolveUniqueSlug($validated['slug'] ?? null, $validated['title'], $blog->id);

        $blog->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'category' => $validated['category'] ?? null,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'is_published' => false,
            'published_at' => null,
            'review_status' => 'pending',
            'reviewed_by' => null,
        ]);

        $blog->loadMissing('astrologer.user');

        User::query()
            ->where('role', 'admin')
            ->get()
            ->each(function (User $admin) use ($blog): void {
                $admin->notify(new BlogSubmittedForReviewAdminNotification($blog));
            });

        return redirect()
            ->route('astrologer.blogs.index')
            ->with('status', 'blog-resubmitted');
    }

    /**
     * Validate blog payload from astrologer create/update forms.
     *
     * @return array<string, mixed>
     */
    private function validateBlog(Request $request, ?Blog $blog = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('blogs', 'slug')->ignore($blog?->id),
            ],
            'category' => ['nullable', 'string', 'max:120'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
        ]);
    }

    /**
     * Generate a unique slug, appending an increment if needed.
     */
    private function resolveUniqueSlug(?string $slug, string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($slug ?: $title);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'blog-post';
        $resolvedSlug = $baseSlug;
        $counter = 2;

        while (
            Blog::query()
                ->where('slug', $resolvedSlug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $resolvedSlug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $resolvedSlug;
    }
}
