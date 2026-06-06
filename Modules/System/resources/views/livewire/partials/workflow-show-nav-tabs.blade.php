<div class="mb-3">
    <div class="card">
        <div class="card-body py-2">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link {{ request('tab', 'steps') === 'steps' ? 'active' : '' }}" href="?tab=steps">
                        Configuration
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') === 'instances' ? 'active' : '' }}" href="?tab=instances">
                        Live Instances
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
