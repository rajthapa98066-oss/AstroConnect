@extends('layouts.admin.master')

@section('admin')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Blog Posts</h4>
            </div>
            <div>
                <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">Add New Blog</a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success">Action completed: {{ str_replace('-', ' ', session('status')) }}</div>
        @endif

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
    </div>
@endsection
