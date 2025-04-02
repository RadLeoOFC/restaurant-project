<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">{{ __('messages.admin_panel') }}</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div>
            <select onchange="window.location.href='/switch-language/' + this.value;">
                @foreach(App\Models\Language::all() as $lang)
                    <option value="{{ $lang->code }}" {{ app()->getLocale() === $lang->code ? 'selected' : '' }}>
                        {{ $lang->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                @auth
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownNav" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <strong>{{ Auth::user()->name ?? __('admin') }}</strong>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdownNav">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('messages.profile') }}</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">{{ __('messages.logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
                @endauth
            </ul>
        </div>
    </div>
</nav>
