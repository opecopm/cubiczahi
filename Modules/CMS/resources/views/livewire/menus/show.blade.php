<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">Manage Menu Items for {{ $menu->name }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @include('components.tabler.alerts')

            <div class="card">
                <div class="card-body">
                    <form wire:submit.prevent="addMenuItems">
                        <div class="table-responsive">
                            <table class="table table-vcenter table-hover card-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Select</th>
                                        <th scope="col">SN</th>
                                        <th scope="col">Title</th>
                                        <th scope="col">URL</th>
                                        <th scope="col">Order</th>
                                        <th scope="col">Parent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menuItems as $key => $menuItem)
                                        <tr>
                                            <td>
                                                <input type="checkbox" wire:model="selectedMenuItems" value="{{ $menuItem->id }}">
                                            </td>
                                            <td>{{ $key + 1 }}</td>
                                            <td><b>{{ $menuItem->title }}</b></td>
                                            <td>{{ $menuItem->url }}</td>
                                            <td>{{ $menuItem->order }}</td>
                                            <td>{{ $menuItem->parent ? $menuItem->parent->title : 'N/A' }}</td>
                                        </tr>
                                        @foreach ($menuItem->children as $subkey => $childItem)
                                        <tr>
                                            <td>
                                                <input type="checkbox" wire:model="selectedMenuItems" value="{{ $childItem->id }}">
                                            </td>
                                            <td>{{ ($key + 1) . '.' . ($subkey + 1) }}</td>
                                            <td>{{ $childItem->title }}</td>
                                            <td>{{ $childItem->url }}</td>
                                            <td>{{ $childItem->order }}</td>
                                            <td>{{ $childItem->parent ? $childItem->parent->title : 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Add Selected Items
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
