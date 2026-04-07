{{-- View: resources\views\pages\admin\blog.blade.php --}}
@extends('layouts.admin.master')

@php
    // Single admin blog page supporting list/create/edit modes.
    $mode = $mode ?? 'list';
    $isEdit = $mode === 'edit';
    $isForm = in_array($mode, ['create', 'edit'], true);
    $formBlog = $isEdit ? ($blog ?? null) : null;
@endphp

@section('admin')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                @if ($isForm)
                    <h4 class="fs-18 fw-semibold m-0">{{ $isEdit ? 'Edit Blog' : 'Create Blog' }}</h4>
                @else
                    <h4 class="fs-18 fw-semibold m-0">Blog Posts</h4>
                @endif
            </div>
            <div>
                @if ($isForm)
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-light">Back to Blogs</a>
                @else
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">Add New Blog</a>
                @endif
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success">Action completed: {{ str_replace('-', ' ', session('status')) }}</div>
        @endif

        {{-- Form mode: used for both create and edit actions. --}}
        @if ($isForm)
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ $isEdit ? route('admin.blogs.update', $formBlog) : route('admin.blogs.store') }}" class="row g-3">
                        @csrf
                        @if ($isEdit)
                            @method('PATCH')
                        @endif

                        <div class="col-md-8">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" id="title" name="title" value="{{ old('title', $formBlog?->title) }}" class="form-control" required>
                            @error('title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="slug" class="form-label">Slug (optional)</label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $formBlog?->slug) }}" class="form-control" placeholder="auto-generated-from-title">
                            @error('slug')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" id="category" name="category" value="{{ old('category', $formBlog?->category) }}" class="form-control" placeholder="Moon Phases">
                            @error('category')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="excerpt" class="form-label">Excerpt</label>
                            <textarea id="excerpt" name="excerpt" rows="2" class="form-control" placeholder="Short intro shown on blog list page">{{ old('excerpt', $formBlog?->excerpt) }}</textarea>
                            @error('excerpt')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="content" class="form-label">Content *</label>
                            <textarea id="content" name="content" rows="12" class="form-control" required>{{ old('content', $formBlog?->content) }}</textarea>
                            @error('content')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $formBlog?->is_published))>
                                <label class="form-check-label" for="is_published">
                                    Show this blog on user blog page
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update Blog' : 'Save Blog' }}</button>
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        @else
            {{-- List mode: moderation table with approve/reject/publish actions. --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Category</th>
                                    <th>Slug</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Published At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($blogs as $blog)
                                    <tr>
                                        <td>{{ $blog->id }}</td>
                                        <td>{{ $blog->title }}</td>
                                        <td>
                                            @if ($blog->astrologer)
                                                {{ $blog->astrologer->user->name }} <span class="badge bg-info ms-1">Astrologer</span>
                                            @else
                                                Admin
                                            @endif
                                        </td>
                                        <td>{{ $blog->category ?: '-' }}</td>
                                        <td><code>{{ $blog->slug }}</code></td>
                                        <td>
                                            @if ($blog->review_status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif ($blog->review_status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($blog->is_published)
                                                <span class="badge bg-success">Visible on user page</span>
                                            @else
                                                <span class="badge bg-secondary">Hidden</span>
                                            @endif
                                        </td>
                                        <td>{{ $blog->published_at?->format('M d, Y h:i A') ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary">Edit</a>

                                                @if ($blog->review_status !== 'approved')
                                                    <form method="POST" action="{{ route('admin.blogs.approve', $blog) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                                    </form>
                                                @endif

                                                @if ($blog->review_status !== 'rejected')
                                                    <form method="POST" action="{{ route('admin.blogs.reject', $blog) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-warning">Reject</button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.blogs.visibility', $blog) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $blog->is_published ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $blog->is_published ? 'Hide' : 'Show' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}" onsubmit="return confirm('Delete this blog post?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No blog posts found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $blogs->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
