<div class="card border-0 shadow-sm" style="position: sticky; top: 20px;">
    <div class="card-body p-4">
        <div style="text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #0d6efd, #0a2463); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem; margin: 0 auto 12px;">
                {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') }}
            </div>
            <h5 class="mb-1" style="font-weight: 700; color: #0a2463;">{{ auth()->user()->name ?? 'Guest' }}</h5>
            <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
        </div>

        <nav class="nav flex-column gap-2">
            <a href="{{ route('customer.dashboard') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6c757d; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#8962;</span>
                Dashboard
            </a>
            <a href="{{ route('customer.profile') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6c757d; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128100;</span>
                Profile
            </a>
            <a href="{{ route('customer.security') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.security') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6c757d; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128274;</span>
                Security
            </a>
            <a href="{{ route('customer.orders.index') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6c757d; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128230;</span>
                Orders
            </a>
            <a href="{{ route('customer.addresses.index') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.addresses.index') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6c757d; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128205;</span>
                Addresses
            </a>
        </nav>

        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            <form method="POST" action="{{ route('customer.logout') }}">
                @csrf
                <button type="submit"
                    style="width:100%; padding: 12px 16px; border-radius: 8px; border: none; background: none; text-align: left; color: #dc3545; cursor: pointer; font-size: inherit; transition: all 0.2s; display: flex; align-items: center;"
                    onmouseover="this.style.backgroundColor='#fff5f5'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#x2192;</span>
                    Sign Out
                </button>
            </form>
        </div>

        <style>
            .nav-link.active {
                background-color: #f0f4f8 !important;
                color: #0d6efd !important;
                border-left-color: #0d6efd !important;
                font-weight: 600;
            }
            .nav-link:hover {
                background-color: #f8f9fa;
                color: #0a2463;
            }
        </style>
    </div>
</div>
