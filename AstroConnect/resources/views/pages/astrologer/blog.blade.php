{{-- View: resources\views\pages\astrologer\blog.blade.php --}}
@extends('layouts.astrologer.master')

@php
    $mode = $mode ?? 'list';
@endphp

@if ($mode === 'list')
    @section('title', 'AstroConnect | Astrologer Blogs')

    @section('content')
    <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 lg:px-8 lg:pb-24 lg:pt-20">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">Astrologer Blogs</p>
                <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">Write articles and submit them for admin review.</h1>
            </div>
            <a href="{{ route('astrologer.blogs.create') }}" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-6 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                Add New Blog
            </a>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl border border-emerald-300/20 bg-emerald-300/10 px-5 py-4 text-sm text-emerald-200">
                Action completed: {{ str_replace('-', ' ', session('status')) }}
            </div>
        @endif

        <div class="rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-300">
                    <thead class="border-b border-white/10 text-xs uppercase tracking-[0.2em] text-slate-400">
                        <tr>
                            <th class="px-3 py-3">Title</th>
                            <th class="px-3 py-3">Review</th>
                            <th class="px-3 py-3">Visibility</th>
                            <th class="px-3 py-3">Updated</th>
                            <th class="px-3 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($blogs as $blog)
                            <tr class="border-b border-white/5">
                                <td class="px-3 py-4">{{ $blog->title }}</td>
                                <td class="px-3 py-4">
                                    <span class="rounded-full border border-white/15 bg-white/5 px-3 py-1 text-xs uppercase tracking-[0.18em] text-white">{{ $blog->review_status }}</span>
                                </td>
                                <td class="px-3 py-4">{{ $blog->is_published ? 'Visible' : 'Hidden' }}</td>
                                <td class="px-3 py-4">{{ $blog->updated_at?->format('M d, Y') }}</td>
                                <td class="px-3 py-4">
                                    <a href="{{ route('astrologer.blogs.edit', $blog) }}" class="text-amber-200 hover:text-amber-100">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-8 text-center text-slate-400">No blogs submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8 [&_nav]:flex [&_nav]:justify-center [&_nav]:text-slate-300 [&_span]:border-white/10 [&_span]:bg-white/5 [&_span]:text-slate-300 [&_a]:border-white/10 [&_a]:bg-white/5 [&_a]:text-slate-200 [&_a:hover]:bg-white/10">
                {{ $blogs->links() }}
            </div>
        </div>
    </section>
    @endsection
@else
    @php
        $isEdit = $mode === 'edit';
        $formBlog = $isEdit ? ($blog ?? null) : null;
    @endphp

    @section('title', $isEdit ? 'AstroConnect | Edit Blog' : 'AstroConnect | Submit Blog')

    @section('content')
    <section class="mx-auto max-w-5xl px-4 pb-20 pt-14 sm:px-6 lg:px-8 lg:pt-20">
        <p class="text-sm uppercase tracking-[0.35em] text-amber-200/70">{{ $isEdit ? 'Edit Blog' : 'Submit Blog' }}</p>
        <h1 class="mt-4 text-5xl text-white sm:text-6xl [font-family:'Cormorant_Garamond',serif]">
            {{ $isEdit ? 'Update blog and resubmit for review.' : 'Create a new blog article for admin review.' }}
        </h1>

        <div class="mt-10 rounded-[2rem] border border-white/10 bg-gradient-to-b from-slate-900 to-slate-950 p-7 shadow-xl shadow-slate-950/30 sm:p-8">
            <form method="POST" action="{{ $isEdit ? route('astrologer.blogs.update', $formBlog) : route('astrologer.blogs.store') }}" class="space-y-6">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                @endif

                <div>
                    <label for="title" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Title</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $formBlog?->title) }}" required class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
                    @error('title')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Slug (optional)</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $formBlog?->slug) }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
                    @error('slug')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Category</label>
                    <input id="category" name="category" type="text" value="{{ old('category', $formBlog?->category) }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">
                    @error('category')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="excerpt" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="3" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">{{ old('excerpt', $formBlog?->excerpt) }}</textarea>
                    @error('excerpt')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Content</label>
                    <textarea id="content" name="content" rows="12" required class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-100 outline-none transition focus:border-amber-300/60 focus:ring-2 focus:ring-amber-300/30">{{ old('content', $formBlog?->content) }}</textarea>
                    @error('content')
                        <p class="mt-2 text-sm text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-amber-300 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-950 transition hover:bg-amber-200">
                        Submit for Review
                    </button>
                    <a href="{{ route('astrologer.blogs.index') }}" class="inline-flex items-center justify-center rounded-full border border-white/15 px-7 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-white transition hover:border-white/30 hover:bg-white/5">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
    @endsection
@endif
