@extends('layouts.admin.master')

@section('admin')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Create Blog</h4>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.blogs.store') }}" class="row g-3">
                    @csrf
                    @include('pages.admin.blogs.partials.form')
                </form>
            </div>
        </div>
    </div>
@endsection
