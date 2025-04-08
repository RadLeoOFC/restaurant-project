<div class="d-flex flex-column flex-shrink-0 p-3 bg-white shadow-sm h-100" style="width: 250px;">
    <a href="{{ url('/') }}" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
        <span class="fs-4 fw-bold">{{ __('messages.home') }}</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto text-start">
        @php
            $isAdmin = auth()->check() && optional(auth()->user()->role)->role_name === 'Admin';
        @endphp

        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-dark' }}">
                <i class="bi bi-house-door"></i>
                {{ $isAdmin ? __('messages.dashboard_title_admin') : __('messages.dashboard_title_user') }}
            </a>
        </li>

        <li>
            <a href="{{ route('desks.index') }}" class="nav-link {{ request()->routeIs('desks.index') ? 'active' : 'text-dark' }}">
                <i class="bi bi-table"></i>
                {{ $isAdmin ? __('messages.manage_desks_admin') : __('messages.manage_desks_user') }}
            </a>
        </li>
        <li>
            <a href="{{ route('desks.map') }}" class="nav-link {{ request()->routeIs('desks.map') ? 'active' : 'text-dark' }}">
                <i class="bi bi-grid"></i>
                {{ __('messages.desk_map') }}
            </a>
        </li>
        <li>
            <a href="{{ route('external-desks.index') }}" class="nav-link {{ request()->routeIs('external-desks.index') ? 'active' : 'text-dark' }}">
                <i class="bi bi-grid"></i>
                {{ __('messages.external_desks') }}
            </a>
        </li>
        <li>
            <a href="{{ route('reservations.index') }}" class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-calendar"></i>
                {{ $isAdmin ? __('messages.manage_reservations_admin') : __('messages.manage_reservations_user') }}
            </a>
        </li>
        <li>
            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : 'text-dark' }}">
                <i class="bi bi-person"></i>
                {{ $isAdmin ? __('messages.manage_customers_admin') : __('messages.manage_customers_user') }}
            </a>
        </li>

        @if($isAdmin)
            <hr>
            <li>
                <a href="{{ route('reports.generate', ['type' => 'daily']) }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-graph-up"></i>
                    {{ __('messages.reports') }}
                </a>
            </li>
            <li>
                <a href="{{ route('report-templates.index') }}" class="nav-link {{ request()->routeIs('report-templates.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-file-earmark-text"></i>
                    {{ __('messages.report_templates') }}
                </a>
            </li>
            <li>
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-shield-lock"></i> {{ __('messages.manage_roles') }}
                </a>
            </li>
            <li>
                <a href="{{ route('translations.index') }}" class="nav-link {{ request()->routeIs('translations.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-translate"></i> {{ __('messages.translations') }}
                </a>
            </li>
            <li>
                <a href="{{ route('languages.index') }}" class="nav-link {{ request()->routeIs('languages.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-translate"></i> {{ __('messages.languages') }}
                </a>
            </li>
            <li>
                <a href="{{ route('notification-templates.index') }}" class="nav-link {{ request()->routeIs('notification-templates.*') ? 'active' : 'text-dark' }}">
                    <i class="bi bi-bell"></i> {{ __('messages.notification_templates') }}
                </a>
            </li>
        @endif
    </ul>
    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
            <strong>{{ auth()->check() ? Auth::user()->name : __('Guest') }}</strong>
        </a>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('messages.profile') }}</a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="dropdown-item text-danger" type="submit">{{ __('messages.logout') }}</button>
                </form>
            </li>
        </ul>
    </div>
</div>
