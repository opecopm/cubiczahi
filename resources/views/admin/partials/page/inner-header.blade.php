@php
    $breadcrumbs = $breadcrumbs ?? [];
    $actionItems = $actionItems ?? (is_array($actions ?? null) ? $actions : []);
    $actionsSlot = isset($actions) && ! is_array($actions) ? $actions : null;
@endphp

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="card">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col">
                        @if(! empty($breadcrumbs))
                            <nav class="d-flex flex-wrap align-items-center gap-2 text-secondary small mb-2" aria-label="breadcrumb">
                                @foreach($breadcrumbs as $breadcrumb)
                                    @if(! $loop->first)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-muted" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6"/></svg>
                                    @endif

                                    @if(! empty($breadcrumb['url']) && empty($breadcrumb['active']))
                                        <a href="{{ $breadcrumb['url'] }}" class="{{ $breadcrumb['class'] ?? 'text-secondary' }} text-decoration-none d-inline-flex align-items-center">
                                            @if(! empty($breadcrumb['icon']))
                                                <i class="{{ $breadcrumb['icon'] === 'back' ? 'ti ti-chevron-left' : $breadcrumb['icon'] }} me-1"></i>
                                            @endif
                                            {{ $breadcrumb['label'] }}
                                        </a>
                                    @else
                                        <span class="badge {{ $breadcrumb['badgeClass'] ?? 'bg-primary-lt' }}" aria-current="page">{{ $breadcrumb['label'] }}</span>
                                    @endif
                                @endforeach
                            </nav>
                        @endif

                        @isset($title)
                            <h2 class="page-title">{{ $title }}</h2>
                        @endisset

                        @isset($meta)
                            <div class="text-muted mt-1">
                                {{ $meta }}
                            </div>
                        @endisset
                    </div>

                    @if(! empty($actionItems) || $actionsSlot)
                        <div class="col-auto ms-auto d-flex flex-wrap align-items-center gap-2">
                            @foreach($actionItems as $action)
                                @php
                                    $actionType = $action['type'] ?? 'link';
                                    $actionTitle = $action['title'] ?? $action['label'] ?? '';
                                    $actionClass = $action['class'] ?? ($actionType === 'badge' ? 'bg-secondary-lt' : 'btn btn-sm btn-outline-secondary');
                                    $actionUrl = $action['url'] ?? null;

                                    if (! $actionUrl && ! empty($action['route'])) {
                                        $actionUrl = route($action['route'], $action['params'] ?? []);
                                    }
                                @endphp

                                @if($actionType === 'badge')
                                    <span class="badge {{ $actionClass }}">{{ $actionTitle }}</span>
                                @elseif($actionType === 'button')
                                    <button type="button" class="{{ $actionClass }}" @if(! empty($action['wireClick'])) wire:click="{{ $action['wireClick'] }}" @endif>
                                        @if(! empty($action['icon']))
                                            <i class="{{ $action['icon'] }} me-1"></i>
                                        @endif
                                        {{ $actionTitle }}
                                    </button>
                                @else
                                    <a href="{{ $actionUrl ?? '#' }}" class="{{ $actionClass }}">
                                        @if(! empty($action['icon']))
                                            <i class="{{ $action['icon'] }} me-1"></i>
                                        @endif
                                        {{ $actionTitle }}
                                    </a>
                                @endif
                            @endforeach

                            @if($actionsSlot)
                                {{ $actionsSlot }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
