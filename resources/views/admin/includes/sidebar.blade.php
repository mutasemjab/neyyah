<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <img src="{{ asset('assets/admin/dist/img/AdminLTELogo.png') }}" alt="Logo"
            class="brand-image img-circle elevation-3" style="opacity:.8">
        <span class="brand-text font-weight-light">Neyyah</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('assets/admin/dist/img/user2-160x160.jpg') }}"
                     class="img-circle elevation-2" alt="User">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ auth()->user()->name }}</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview"
                role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <p>{{ __('messages.Dashboard') }}</p>
                    </a>
                </li>

                {{-- ── User Management ── --}}
                <li class="nav-header">{{ __('messages.user_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-users nav-icon"></i>
                        <p>{{ __('messages.Users') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.verifications.index') }}"
                       class="nav-link {{ request()->routeIs('admin.verifications.*') ? 'active' : '' }}">
                        <i class="fas fa-id-card nav-icon"></i>
                        <p>{{ __('messages.Identity_Verifications') }}</p>
                    </a>
                </li>

                {{-- ── Matching & Moderation ── --}}
                <li class="nav-header">{{ __('messages.matching_moderation') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.match-requests.index') }}"
                       class="nav-link {{ request()->routeIs('admin.match-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-heart nav-icon"></i>
                        <p>{{ __('messages.Match_Requests') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.conversations.index') }}"
                       class="nav-link {{ request()->routeIs('admin.conversations.*') ? 'active' : '' }}">
                        <i class="fas fa-comments nav-icon"></i>
                        <p>{{ __('messages.Conversations') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reports.index') }}"
                       class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="fas fa-flag nav-icon"></i>
                        <p>{{ __('messages.Reports') }}</p>
                    </a>
                </li>

                {{-- ── Marketplace ── --}}
                <li class="nav-header">سوق الوساطة والاستشارة</li>

                <li class="nav-item">
                    <a href="{{ route('admin.matchmakers.index') }}"
                       class="nav-link {{ request()->routeIs('admin.matchmakers.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie nav-icon"></i>
                        <p>الوسطاء</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.consultants.index') }}"
                       class="nav-link {{ request()->routeIs('admin.consultants.*') ? 'active' : '' }}">
                        <i class="fas fa-user-md nav-icon"></i>
                        <p>المستشارون</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.private-requests.index') }}"
                       class="nav-link {{ request()->routeIs('admin.private-requests.*') ? 'active' : '' }}">
                        <i class="fas fa-handshake nav-icon"></i>
                        <p>الطلبات الخاصة</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.consultations.index') }}"
                       class="nav-link {{ request()->routeIs('admin.consultations.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check nav-icon"></i>
                        <p>الجلسات الاستشارية</p>
                    </a>
                </li>

                {{-- ── Notifications ── --}}
                <li class="nav-header">{{ __('messages.notifications') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.notifications.index') }}"
                       class="nav-link {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                        <i class="fas fa-bell nav-icon"></i>
                        <p>{{ __('messages.Notifications') }}</p>
                    </a>
                </li>

                {{-- ── Content Management ── --}}
                <li class="nav-header">{{ __('messages.content_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.interests.index') }}"
                       class="nav-link {{ request()->routeIs('admin.interests.*') ? 'active' : '' }}">
                        <i class="fas fa-tags nav-icon"></i>
                        <p>{{ __('messages.Interests') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.guided-questions.index') }}"
                       class="nav-link {{ request()->routeIs('admin.guided-questions.*') ? 'active' : '' }}">
                        <i class="fas fa-question-circle nav-icon"></i>
                        <p>{{ __('messages.Guided_Questions') }}</p>
                    </a>
                </li>

                {{-- ── Admin & Roles ── --}}
                <li class="nav-header">{{ __('messages.user_management') }}</li>

                <li class="nav-item">
                    <a href="{{ route('admin.login.edit', auth()->user()->id) }}"
                       class="nav-link {{ request()->routeIs('admin.login.edit') ? 'active' : '' }}">
                        <i class="fas fa-user nav-icon"></i>
                        <p>{{ __('messages.Admin_account') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.role.index') }}"
                       class="nav-link {{ request()->routeIs('admin.role.*') ? 'active' : '' }}">
                        <i class="fas fa-shield-alt nav-icon"></i>
                        <p>{{ __('messages.Roles') }}</p>
                    </a>
                </li>

                @if (
                    $user->can('employee-table') ||
                    $user->can('employee-add') ||
                    $user->can('employee-edit') ||
                    $user->can('employee-delete'))
                <li class="nav-item">
                    <a href="{{ route('admin.employee.index') }}"
                       class="nav-link {{ request()->routeIs('admin.employee.*') ? 'active' : '' }}">
                        <i class="far fa-id-badge nav-icon"></i>
                        <p>{{ __('messages.Employee') }}</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
