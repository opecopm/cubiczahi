<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle"><a href="{{ route('admin.system.menus.index') }}">Menus</a></div>
                    <h2 class="page-title">{{ $menu->name }}</h2>
                </div>
                <div class="col-auto ms-auto">
                    <a href="{{ route('admin.system.menus.index') }}" class="btn btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible mb-3">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Manage Menu Items for "{{ $menu->name }}"</h3>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="addMenuItems">
                        <div class="table-responsive">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th style="width:40px">Select</th>
                                        <th>SN</th>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Order</th>
                                        <th>Parent</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menuItems as $key => $menuItem)
                                        <tr>
                                            <td>
                                                <input class="form-check-input" type="checkbox" wire:model="selectedMenuItems" value="{{ $menuItem->id }}">
                                            </td>
                                            <td class="text-secondary">{{ $key + 1 }}</td>
                                            <td class="fw-bold">{{ $menuItem->title }}</td>
                                            <td class="text-secondary">{{ $menuItem->url }}</td>
                                            <td>{{ $menuItem->order }}</td>
                                            <td class="text-secondary">{{ $menuItem->parent ? $menuItem->parent->title : '—' }}</td>
                                        </tr>
                                        @foreach ($menuItem->children as $subkey => $childItem)
                                            <tr class="table-active">
                                                <td>
                                                    <input class="form-check-input" type="checkbox" wire:model="selectedMenuItems" value="{{ $childItem->id }}">
                                                </td>
                                                <td class="text-secondary">{{ ($key + 1) . '.' . ($subkey + 1) }}</td>
                                                <td class="ps-4 text-secondary">{{ $childItem->title }}</td>
                                                <td class="text-secondary">{{ $childItem->url }}</td>
                                                <td>{{ $childItem->order }}</td>
                                                <td class="text-secondary">{{ $childItem->parent ? $childItem->parent->title : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Add Selected Items</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
