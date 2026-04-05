@php
    $blog = $blog ?? null;
@endphp

<div class="col-md-8">
    <label for="title" class="form-label">Title *</label>
    <input type="text" id="title" name="title" value="{{ old('title', $blog?->title) }}" class="form-control" required>
    @error('title')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4">
    <label for="slug" class="form-label">Slug (optional)</label>
    <input type="text" id="slug" name="slug" value="{{ old('slug', $blog?->slug) }}" class="form-control" placeholder="auto-generated-from-title">
    @error('slug')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4">
    <label for="category" class="form-label">Category</label>
    <input type="text" id="category" name="category" value="{{ old('category', $blog?->category) }}" class="form-control" placeholder="Moon Phases">
    @error('category')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-8">
    <label for="excerpt" class="form-label">Excerpt</label>
    <textarea id="excerpt" name="excerpt" rows="2" class="form-control" placeholder="Short intro shown on blog list page">{{ old('excerpt', $blog?->excerpt) }}</textarea>
    @error('excerpt')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <label for="content" class="form-label">Content *</label>
    <textarea id="content" name="content" rows="12" class="form-control" required>{{ old('content', $blog?->content) }}</textarea>
    @error('content')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="col-12">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" @checked(old('is_published', $blog?->is_published))>
        <label class="form-check-label" for="is_published">
            Show this blog on user blog page
        </label>
    </div>
</div>

<div class="col-12 d-flex gap-2">
    <button type="submit" class="btn btn-primary">Save Blog</button>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-light">Cancel</a>
</div>
