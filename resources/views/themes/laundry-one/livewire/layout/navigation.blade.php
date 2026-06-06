<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top" style="padding: 1rem 0;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 text-primary" href="{{ route('home') }}" wire:navigate>
            <span style="background: linear-gradient(135deg,#0d6efd,#0dcaf0); -webkit-background-clip:text; -webkit-text-fill-color:transparent;">
                &#9910; {{ config('app.name', 'LaundryPro') }}
            </span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'text-primary fw-semibold' : 'text-dark' }}"
                       href="{{ route('home') }}" wire:navigate>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('catalog.*') ? 'text-primary fw-semibold' : 'text-dark' }}"
                       href="{{ route('catalog.index') }}" wire:navigate>Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'text-primary fw-semibold' : 'text-dark' }}"
                       href="{{ route('about') }}" wire:navigate>About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'text-primary fw-semibold' : 'text-dark' }}"
                       href="{{ route('contact') }}" wire:navigate>Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark"
                       href="{{ request()->routeIs('home') ? '#pricing' : route('home').'#pricing' }}">Pricing</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                <a href="{{ route('customer.order.builder') }}" wire:navigate
                   class="btn btn-primary px-4 {{ request()->routeIs('order.*') ? 'active' : '' }}">
                    &#128717; Order Now
                </a>
                @auth
                    <a href="{{ route('customer.dashboard') }}" wire:navigate class="btn btn-outline-primary px-4">Dashboard</a>
                @else
                    <a href="{{ route('customer.login') }}" wire:navigate class="btn btn-outline-secondary px-3">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
