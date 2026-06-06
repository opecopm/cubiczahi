<div wire:poll.30s>
    {{-- Page header --}}
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        <i class="ti ti-layout-dashboard me-2 text-primary"></i>Dashboard
                    </h2>
                    <div class="text-muted mt-1">Welcome back! Here's what's happening at your laundry.</div>
                </div>
                <div class="col-auto ms-auto">
                    <div class="btn-group" role="group">
                        <button type="button" wire:click="$set('period','today')"
                            class="btn btn-sm {{ $period === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</button>
                        <button type="button" wire:click="$set('period','week')"
                            class="btn btn-sm {{ $period === 'week' ? 'btn-primary' : 'btn-outline-secondary' }}">Week</button>
                        <button type="button" wire:click="$set('period','month')"
                            class="btn btn-sm {{ $period === 'month' ? 'btn-primary' : 'btn-outline-secondary' }}">Month</button>
                        <button type="button" wire:click="$set('period','year')"
                            class="btn btn-sm {{ $period === 'year' ? 'btn-primary' : 'btn-outline-secondary' }}">Year</button>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.selling.sales-orders.create') }}" class="btn btn-primary d-none d-sm-inline-flex">
                        <i class="ti ti-plus me-1"></i>New Order
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            {{-- ── KPI cards ── --}}
            <div class="row row-deck row-cards mb-3">

                {{-- Total Orders --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <i class="ti ti-clipboard-list fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Total Orders</div>
                                    <div class="text-muted small">This {{ $period }}</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="h1 mb-0">{{ number_format($totalOrders) }}</div>
                                <a href="{{ route('admin.selling.sales-orders.index') }}" class="text-muted small">View all orders →</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revenue --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar">
                                        <i class="ti ti-currency-dollar fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Revenue</div>
                                    <div class="text-muted small">This {{ $period }}</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="h1 mb-0">{{ number_format($totalRevenue, 2) }}</div>
                                <a href="{{ route('admin.selling.sales-invoices.index') }}" class="text-muted small">View invoices →</a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pending Orders --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-yellow text-white avatar">
                                        <i class="ti ti-clock-hour-4 fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Pending Orders</div>
                                    <div class="text-muted small">Needs attention</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="h1 mb-0">{{ number_format($pendingOrders) }}</div>
                                @if($readyOrders > 0)
                                    <span class="text-teal small"><i class="ti ti-package me-1"></i>{{ $readyOrders }} ready for pickup</span>
                                @else
                                    <span class="text-muted small">None ready for pickup</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customers --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-cyan text-white avatar">
                                        <i class="ti ti-users fs-2"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Customers</div>
                                    <div class="text-muted small">Total registered</div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="h1 mb-0">{{ number_format($totalCustomers) }}</div>
                                @if($newCustomers > 0)
                                    <span class="text-green small"><i class="ti ti-trending-up me-1"></i>+{{ $newCustomers }} new this {{ $period }}</span>
                                @else
                                    <span class="text-muted small">No new customers this {{ $period }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Secondary KPI row ── --}}
            <div class="row row-deck row-cards mb-3">

                {{-- Unpaid invoices --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card {{ $unpaidInvoices > 0 ? 'border-danger' : '' }}">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="{{ $unpaidInvoices > 0 ? 'bg-danger-lt text-danger' : 'bg-secondary-lt text-secondary' }} avatar avatar-sm">
                                    <i class="ti ti-receipt-off"></i>
                                </span>
                                <div class="flex-fill">
                                    <div class="text-muted small">Unpaid Invoices</div>
                                    <div class="fw-bold">{{ $unpaidInvoices }} invoices</div>
                                </div>
                                <div class="ms-auto text-end">
                                    <div class="{{ $unpaidInvoices > 0 ? 'text-danger' : 'text-muted' }} fw-bold">{{ number_format($overdueAmount, 2) }}</div>
                                    <div class="text-muted small">outstanding</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active Services --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="bg-teal-lt text-teal avatar avatar-sm">
                                    <i class="ti ti-shirt"></i>
                                </span>
                                <div class="flex-fill">
                                    <div class="text-muted small">Active Services</div>
                                    <div class="fw-bold">{{ $activeServices }} services</div>
                                </div>
                                <div class="ms-auto">
                                    <a href="{{ route('admin.inventory.items.index') }}" class="btn btn-sm btn-ghost-secondary">Manage</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ready for pickup --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card {{ $readyOrders > 0 ? 'border-teal' : '' }}">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="{{ $readyOrders > 0 ? 'bg-teal text-white' : 'bg-secondary-lt text-secondary' }} avatar avatar-sm">
                                    <i class="ti ti-package"></i>
                                </span>
                                <div class="flex-fill">
                                    <div class="text-muted small">Ready for Pickup</div>
                                    <div class="fw-bold">{{ $readyOrders }} orders</div>
                                </div>
                                @if($readyOrders > 0)
                                <div class="ms-auto">
                                    <span class="badge bg-teal-lt text-teal">Action needed</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick create --}}
                <div class="col-sm-6 col-lg-3">
                    <div class="card bg-primary-lt">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="flex-fill">
                                    <div class="fw-bold text-primary">Quick Actions</div>
                                </div>
                            </div>
                            <div class="mt-2 d-flex flex-wrap gap-1">
                                <a href="{{ route('admin.selling.sales-orders.create') }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-plus me-1"></i>Order
                                </a>
                                <a href="{{ route('admin.crm.customers.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-user-plus me-1"></i>Customer
                                </a>
                                <a href="{{ route('admin.selling.sales-invoices.create') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="ti ti-file-invoice me-1"></i>Invoice
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Order pipeline + Revenue chart ── --}}
            <div class="row row-deck row-cards mb-3">

                {{-- Order Status Pipeline --}}
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-filter me-2"></i>Order Pipeline</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">

                                @php
                                    $statuses = [
                                        'draft'      => ['label' => 'Draft',             'icon' => 'ti-file',          'color' => 'secondary'],
                                        'new'        => ['label' => 'New',               'icon' => 'ti-bell',          'color' => 'blue'],
                                        'confirmed'  => ['label' => 'Confirmed',         'icon' => 'ti-circle-check',  'color' => 'info'],
                                        'processing' => ['label' => 'Processing',        'icon' => 'ti-loader',        'color' => 'warning'],
                                        'ready'      => ['label' => 'Ready for Pickup',  'icon' => 'ti-package',       'color' => 'teal'],
                                        'picked_up'  => ['label' => 'Picked Up',         'icon' => 'ti-truck-delivery','color' => 'green'],
                                        'delivered'  => ['label' => 'Delivered',         'icon' => 'ti-circle-check',  'color' => 'success'],
                                        'canceled'   => ['label' => 'Canceled',          'icon' => 'ti-x',             'color' => 'danger'],
                                    ];
                                    $totalStatusCount = array_sum($statusCounts) ?: 1;
                                @endphp

                                @foreach($statuses as $key => $info)
                                    @php $count = $statusCounts[$key] ?? 0; @endphp
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="avatar avatar-sm bg-{{ $info['color'] }}-lt text-{{ $info['color'] }}">
                                                    <i class="ti {{ $info['icon'] }}"></i>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="d-flex justify-content-between">
                                                    <span class="small fw-medium">{{ $info['label'] }}</span>
                                                    <span class="badge bg-{{ $info['color'] }}-lt text-{{ $info['color'] }}">{{ $count }}</span>
                                                </div>
                                                <div class="progress progress-xs mt-1">
                                                    <div class="progress-bar bg-{{ $info['color'] }}"
                                                         style="width: {{ $totalStatusCount ? round(($count / $totalStatusCount) * 100) : 0 }}%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.selling.sales-orders.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                View All Orders
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Revenue & Orders Chart --}}
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-chart-bar me-2"></i>Revenue (Last 6 Months)</h3>
                        </div>
                        <div class="card-body">
                            <div id="chart-revenue" style="min-height: 280px;"
                                wire:ignore
                                data-months="{{ json_encode($chartMonths) }}"
                                data-revenue="{{ json_encode($chartRevenue) }}"
                                data-orders="{{ json_encode($chartOrders) }}">
                            </div>
                            @if($deliveryBreakdown->isNotEmpty())
                            @php
                                $deliveryTotal = $deliveryBreakdown->sum('count') ?: 1;
                                $deliveryColors = ['blue','green','orange','red','purple','teal','cyan','yellow'];
                            @endphp
                            <div class="border-top pt-3 mt-1">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-medium text-uppercase" style="letter-spacing:.05em;">
                                        <i class="ti ti-truck me-1"></i>Orders by Delivery Method
                                    </span>
                                    <span class="text-muted small">{{ $deliveryTotal }} total</span>
                                </div>
                                <div class="row g-2">
                                    @foreach($deliveryBreakdown as $i => $row)
                                        @php
                                            $methodName = $row->deliveryMethod?->name ?? 'No method';
                                            $pct = round(($row->count / $deliveryTotal) * 100);
                                            $color = $deliveryColors[$i % count($deliveryColors)];
                                        @endphp
                                        <div class="col-sm-6">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="d-flex align-items-center gap-1 text-truncate" style="max-width:70%;">
                                                    <span class="badge bg-{{ $color }}" style="width:8px;height:8px;border-radius:50%;padding:0;flex-shrink:0;"></span>
                                                    <span class="small fw-medium text-truncate" title="{{ $methodName }}">{{ $methodName }}</span>
                                                </span>
                                                <span class="small text-muted ms-2 flex-shrink-0">{{ $row->count }} &middot; {{ $pct }}%</span>
                                            </div>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar bg-{{ $color }}" style="width:{{ $pct }}%" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── Recent Orders + Delivery Breakdown ── --}}
            <div class="row row-deck row-cards">

                {{-- Recent Orders --}}
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-list-details me-2"></i>Recent Orders</h3>
                            <div class="card-actions">
                                <a href="{{ route('admin.selling.sales-orders.index') }}" class="btn btn-sm btn-ghost-secondary">View all</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table table-hover">
                                <thead>
                                    <tr>
                                        <th>Reference</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="w-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.selling.sales-orders.show', $order->id) }}" class="text-primary fw-medium">
                                                    {{ $order->reference }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-xs bg-secondary-lt me-2">
                                                        {{ strtoupper(substr($order->customer->name ?? 'N', 0, 1)) }}
                                                    </span>
                                                    <span class="text-muted small">{{ $order->customer->name ?? '—' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-muted small">{{ \Carbon\Carbon::parse($order->order_date)->format('d M Y') }}</td>
                                            <td class="fw-medium">{{ number_format($order->total, 2) }}</td>
                                            <td>
                                                <span class="badge {{ $order->getStatusBadgeClass() }}">
                                                    {{ $order->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.selling.sales-orders.show', $order->id) }}" class="btn btn-sm btn-ghost-secondary">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                <i class="ti ti-clipboard-off fs-2 d-block mb-2"></i>
                                                No orders yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Delivery Method Breakdown + Summary --}}
                <div class="col-lg-4">

                    {{-- Summary card --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="ti ti-report-analytics me-2"></i>Summary</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0">
                                <dt class="col-7 text-muted small">Total Orders ({{ ucfirst($period) }})</dt>
                                <dd class="col-5 text-end fw-bold">{{ number_format($totalOrders) }}</dd>

                                <dt class="col-7 text-muted small">Revenue ({{ ucfirst($period) }})</dt>
                                <dd class="col-5 text-end fw-bold text-green">{{ number_format($totalRevenue, 2) }}</dd>

                                <dt class="col-7 text-muted small">Active Customers</dt>
                                <dd class="col-5 text-end fw-bold">{{ number_format($totalCustomers) }}</dd>

                                <dt class="col-7 text-muted small">Active Services</dt>
                                <dd class="col-5 text-end fw-bold">{{ number_format($activeServices) }}</dd>

                                @if($unpaidInvoices > 0)
                                <dt class="col-7 text-muted small text-danger">Outstanding Amount</dt>
                                <dd class="col-5 text-end fw-bold text-danger">{{ number_format($overdueAmount, 2) }}</dd>
                                @endif
                            </dl>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('admin.selling.sales-invoices.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="ti ti-file-invoice me-1"></i>Manage Invoices
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>

@push('js')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initRevenueChart();
    });

    document.addEventListener('livewire:navigated', function () {
        initRevenueChart();
    });

    document.addEventListener('livewire:updated', function () {
        initRevenueChart();
    });

    function initRevenueChart() {
        const el = document.getElementById('chart-revenue');
        if (!el) return;

        // Destroy previous instance if exists
        if (window._revenueChart) {
            window._revenueChart.destroy();
        }

        const months  = JSON.parse(el.dataset.months  || '[]');
        const revenue = JSON.parse(el.dataset.revenue || '[]');
        const orders  = JSON.parse(el.dataset.orders  || '[]');

        window._revenueChart = new ApexCharts(el, {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false },
                fontFamily: 'inherit',
                stacked: false,
            },
            series: [
                {
                    name: 'Revenue',
                    type: 'bar',
                    data: revenue,
                },
                {
                    name: 'Orders',
                    type: 'line',
                    data: orders,
                },
            ],
            xaxis: {
                categories: months,
                labels: { style: { fontSize: '12px' } },
            },
            yaxis: [
                {
                    title: { text: 'Revenue' },
                    labels: {
                        formatter: val => val.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }),
                    },
                },
                {
                    opposite: true,
                    title: { text: 'Orders' },
                    labels: { formatter: val => Math.round(val) },
                },
            ],
            colors: ['#206bc4', '#f59f00'],
            plotOptions: {
                bar: { borderRadius: 4, columnWidth: '55%' },
            },
            dataLabels: { enabled: false },
            grid: {
                borderColor: '#e9ecef',
                strokeDashArray: 4,
            },
            stroke: {
                width: [0, 2],
                curve: 'smooth',
            },
            markers: { size: 4 },
            tooltip: {
                shared: true,
                intersect: false,
            },
            legend: { position: 'top' },
        });

        window._revenueChart.render();
    }
</script>
@endpush
