<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminBlogController extends Controller
{
    public function index(): View
    {
        $blogs = Blog::query()
            ->latest()
            ->paginate(15);

        return view('pages.admin.blogs.index', [
            'blogs' => $blogs,
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.blogs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateBlog($request);
        $validated['slug'] = $this->resolveUniqueSlug($validated['slug'] ?? null, $validated['title']);

        if ($validated['is_published']) {
            $validated['published_at'] = Carbon::now();
        }

        Blog::create($validated);

        return redirect()
            ->route('admin.blogs.index')
            ->with('status', 'blog-created');
    }

    public function edit(Blog $blog): View
    {
        return view('pages.admin.blogs.edit', [
            'blog' => $blog,
        ]);
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $validated = $this->validateBlog($request, $blog);
        $validated['slug'] = $this->resolveUniqueSlug($validated['slug'] ?? null, $validated['title'], $blog->id);

        if ($validated['is_published'] && ! $blog->is_published) {
            $validated['published_at'] = Carbon::now();
        }

        if (! $validated['is_published']) {
            $validated['published_at'] = null;
        }

        $blog->update($validated);

        return redirect()
            ->route('admin.blogs.index')
            ->with('status', 'blog-updated');
    }

    public function toggleVisibility(Blog $blog): RedirectResponse
    {
        $isPublished = ! $blog->is_published;

        $blog->update([
            'is_published' => $isPublished,
            'published_at' => $isPublished ? Carbon::now() : null,
        ]);

        return redirect()
            ->route('admin.blogs.index')
            ->with('status', $isPublished ? 'blog-published' : 'blog-unpublished');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $blog->delete();

        return redirect()
            ->route('admin.blogs.index')
            ->with('status', 'blog-deleted');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateBlog(Request $request, ?Blog $blog = null): array
    {
        $validated = $request->validate([
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
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = (bool) ($validated['is_published'] ?? false);

        return $validated;
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
