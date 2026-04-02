<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = Blog::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9);

        return view('pages.blog', [
            'posts' => $posts,
        ]);
    }

    public function show(Blog $blog): View
    {
        abort_unless($blog->is_published, 404);

        $relatedPosts = Blog::query()
            ->where('is_published', true)
            ->whereKeyNot($blog->id)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(3)
            ->get();

        return view('pages.blog-show', [
            'post' => $blog,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
