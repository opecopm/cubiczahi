<div class="card border-0 shadow-sm" style="position: sticky; top: 20px;">
    <div class="card-body p-4">
        <div style="text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #059669, #064e3b); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem; margin: 0 auto 12px;">
                {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') }}
            </div>
            <h5 class="mb-1" style="font-weight: 700; color: #064e3b;">{{ auth()->user()->name ?? 'Guest' }}</h5>
            <small class="text-muted">{{ auth()->user()->email ?? '' }}</small>
        </div>

        <nav class="nav flex-column gap-2 account-sidebar-nav">
            <a href="{{ lroute('customer.dashboard') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6b7280; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#8962;</span>
                {{ __('account.dashboard') }}
            </a>
            <a href="{{ lroute('customer.profile') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6b7280; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128100;</span>
                {{ __('account.profile') }}
            </a>
            <a href="{{ lroute('customer.security') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.security') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6b7280; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128274;</span>
                {{ __('account.security') }}
            </a>
            <a href="{{ lroute('customer.orders.index') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.orders.*') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6b7280; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128230;</span>
                {{ __('account.orders') }}
            </a>
            <a href="{{ lroute('customer.addresses.index') }}" wire:navigate
               class="nav-link {{ request()->routeIs('customer.addresses.index') ? 'active' : '' }}"
               style="padding: 12px 16px; border-radius: 8px; color: #6b7280; text-decoration: none; border-left: 3px solid transparent; transition: all 0.2s;">
                <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#128205;</span>
                {{ __('account.addresses') }}
            </a>
        </nav>

        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #e5e7eb;">
            <form method="POST" action="{{ lroute('customer.logout') }}">
                @csrf
                <button type="submit"
                    style="width:100%; padding: 12px 16px; border-radius: 8px; border: none; background: none; text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }}; color: #dc3545; cursor: pointer; font-size: inherit; transition: all 0.2s; display: flex; align-items: center;"
                    onmouseover="this.style.backgroundColor='#fff5f5'"
                    onmouseout="this.style.backgroundColor='transparent'">
                    <span style="display: inline-block; width: 20px; margin-right: 10px; text-align: center;">&#x2192;</span>
                    {{ __('account.sign_out') }}
                </button>
            </form>
        </div>
    </div>
</div>
