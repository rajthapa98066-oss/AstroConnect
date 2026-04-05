<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AstrologerBlogController extends Controller
{
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

    public function create(): View
    {
        return view('pages.astrologer.blog', [
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBlog($request);
        $validated['slug'] = $this->resolveUniqueSlug($validated['slug'] ?? null, $validated['title']);

        Blog::create([
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
        ]);

        return redirect()
            ->route('astrologer.blogs.index')
            ->with('status', 'blog-submitted');
    }

    public function edit(Request $request, Blog $blog): View
    {
        $astrologer = $request->user()->astrologer;
        abort_unless($blog->astrologer_id === $astrologer->id, 403);

        return view('pages.astrologer.blog', [
            'mode' => 'edit',
            'blog' => $blog,
        ]);
    }

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

        return redirect()
            ->route('astrologer.blogs.index')
            ->with('status', 'blog-resubmitted');
    }

    /**
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
