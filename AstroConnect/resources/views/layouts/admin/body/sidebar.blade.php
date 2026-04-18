{{-- View: resources\views\layouts\admin\body\sidebar.blade.php --}}
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        @php
            $usersCount = \App\Models\User::count();
            $astrologerCount = \App\Models\Astrologer::count();
            $blogsCount = \App\Models\Blog::count();
            $reportsCount = \App\Models\AstrologerReport::where('status', 'pending')->count();
        @endphp

        <div id="sidebar-menu">
            <div class="logo-box">
                <a href="{{ route('admin.dashboard') }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="AstroConnect" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-light.png') }}" alt="AstroConnect" height="24">
                    </span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset('backend/assets/images/logo-sm.png') }}" alt="AstroConnect" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset('backend/assets/images/logo-dark.png') }}" alt="AstroConnect" height="24">
                    </span>
                </a>
            </div>

            <ul id="side-menu">
                <li class="menu-title">Admin</li>

                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i data-feather="home"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.astrologers.index') }}" class="{{ request()->routeIs('admin.astrologers.*') ? 'active' : '' }}">
                        <i data-feather="user-check"></i>
                        <span>Astrologer Applications</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.users.index') }}" class="d-flex align-items-center {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i data-feather="users"></i>
                        <span>Users</span>
                        <span class="badge bg-primary ms-auto">{{ $usersCount }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.astrologers.index') }}" class="d-flex align-items-center {{ request()->routeIs('admin.astrologers.*') ? 'active' : '' }}">
                        <i data-feather="user-check"></i>
                        <span>Astrologers</span>
                        <span class="badge bg-info ms-auto">{{ $astrologerCount }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.reports.index') }}" class="d-flex align-items-center {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i data-feather="alert-triangle"></i>
                        <span>Reports</span>
                        <span class="badge bg-danger ms-auto">{{ $reportsCount }}</span>
                    </a>
                </li>



                <li>
                    <a href="{{ route('admin.blogs.index') }}" class="d-flex align-items-center {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}">
                        <i data-feather="book-open"></i>
                        <span>Blogs</span>
                        <span class="badge bg-warning text-dark ms-auto">{{ $blogsCount }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
