<div class="topbar-custom">
    <div class="container-xxl">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link ps-0" type="button" aria-label="Toggle sidebar menu">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li class="d-none d-sm-flex">
                    <a href="{{ route('home') }}" class="btn nav-link">View Site</a>
                </li>

                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ asset('backend/assets/images/users/user-11.jpg') }}" alt="user-image" class="rounded-circle">
                        <span class="pro-user-name ms-1">
                            {{ Auth::user()?->name ?? 'Admin' }} <i class="mdi mdi-chevron-down"></i>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">Welcome!</h6>
                        </div>

                        <a href="{{ route('admin.dashboard') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-view-dashboard-outline fs-16 align-middle"></i>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('admin.astrologers.index') }}" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-check-outline fs-16 align-middle"></i>
                            <span>Astrologer Applications</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item notify-item">
                                <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
